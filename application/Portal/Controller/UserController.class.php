<?php

/**
 * 猫女
 */

namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * UserController
 * @author Ye Weijie;
 */
class UserController extends HomebaseController
{

    /**
     * UserController index()
     */
    public function index()
    {
        // dump(session());die;
        $usrinfo = session('user');
        $usr_id = $usrinfo['id'];
        // $collects = M("usr_collection")->where("id={$usr_id}")->select();
        
        // $this->assign('info',$usrinfo);
        // $this->assign("ids",$collects);
        // $this->display(":my");
        $id=sp_get_current_userid();

        $users_model=M("Users");

        // 获取未通过试用信息
        $notry = M("comments")->where("status = 0")->select();
        // 获取已通过试用信息
        $try = sp_get_comments("field:*;order:createtime desc;");
        // 获取报告
        $report = M("report")->where('uid='.$usr_id)->order("id desc")->select();

        $this->assign('noentry',$notry);
        $this->assign('report',$report);
        $this->assign('product',$try);
        $this->assign('info',$usrinfo);
        $this->assign("ids",$collects);
        $user=$users_model->where(array("id"=>$id))->find();

        if (empty($user)) {
            $this->error("查无此人！");
        }
        $this->assign('info', $user);
        $this->display(":my");
    }

    /**
     * 用户注册
     *
     * @return void
     */
    public function register()
    {
        if (empty($_POST)) {
            return $this->display(":zhuce");
        }

        $usrModel = M("users");
        $usrArray = $usrModel->select();

        $data['user_login'] = I("post.usr");
        $pwd = I("post.psw");
        $repsw = I("post.repsw");
        $data['mail'] = I("post.mail");
        $verify = I("post.verify");

        $relay = $usrModel->where("user_login='{$data['user_login']}'")->find();

        if ($relay) {
            die("<script>alert('用户名已存在');history.go(-1)</script>");
        }

        if ($repsw !== $pwd) {
            die("<script>alert('两次密码输入不一致，请重新输入!');history.go(-1)</script>");
        }
        $data['user_pass'] = sp_password($pwd);
        if (!sp_check_verify_code($verify)) {
            die("<script>alert('验证码错误！');history.go(-1)</script>");
        }

        if ($usrModel->data($data)->add()) {
            echo "<script>alert('注册成功');window.location.href='".U('User/login')."'</script>";
        } else {
            echo "<script>alert('注册失败，请过会儿重试');history.go(-1);</script>";
        }
    }

    /**
     * 用户登录+登录提交
     *
     * @return void
     */
    public function login()
    {
        //用户登录
        if (empty($_POST)) {
            $redirect=$_SERVER['HTTP_REFERER'];
            session('login_http_referer', $redirect);
            
            if (sp_is_user_login()) { //已经登录时直接跳到首页
                redirect(__ROOT__."/");
            } else {
                return $this->display(":login");
            }
        }
        
        //登录验证
        $users_model = M("users");
        $rules = array(
                //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
                array('usr', 'require', '用户名不能为空！', 1 ),
                array('pwd','require','密码不能为空！',1),
        
        );
        if ($users_model->validate($rules)->create()===false) {
            $this->error($users_model->getError());
        }
        
        $username=I('post.usr');
        $password=I('post.pwd');
        $where = array("user_status"=>1);
        $where['user_login']=$username;
        $result = $users_model->where($where)->find();
        $ucenter_syn=C("UCENTER_ENABLED");
        
        $ucenter_old_user_login=false;
         
        $ucenter_login_ok=false;
        if ($ucenter_syn) {
            cookie("thinkcmf_auth", "");
            include UC_CLIENT_ROOT."client.php";
            list($uc_uid, $username, $password, $email)=uc_user_login($username, $password);
             
            if ($uc_uid>0) {
                if (!$result) {
                    $data=array(
                        'user_login' => $username,
                        'user_email' => $email,
                        'user_pass' => sp_password($password),
                        'last_login_ip' => get_client_ip(0, true),
                        'create_time' => time(),
                        'last_login_time' => time(),
                        'user_status' => '1',
                        'user_type'=>2,
                    );
                    $id= $users_model->add($data);
                    $data['id']=$id;
                    $result=$data;
                }
            } else {
                switch ($uc_uid) {
                    case "-1"://用户不存在，或者被删除
                        if ($result) {//本应用已经有这个用户
                            if (sp_compare_password($password, $result['user_pass'])) {
                                //本应用已经有这个用户,且密码正确，同步用户
                                $uc_uid2=uc_user_register($username, $password, $result['user_email']);
                                if ($uc_uid2<0) {
                                    $uc_register_errors=array(
                                        "-1"=>"用户名不合法",
                                        "-2"=>"包含不允许注册的词语",
                                        "-3"=>"用户名已经存在",
                                        "-4"=>"Email格式有误",
                                        "-5"=>"Email不允许注册",
                                        "-6"=>"该Email已经被注册",
                                    );
                                    $this->error("同步用户失败--".$uc_register_errors[$uc_uid2]);
                                }
                                $uc_uid=$uc_uid2;
                            } else {
                                $this->error("密码错误！");
                            }
                        }
        
                        break;
                    case -2://密码错
                        if ($result) {//本应用已经有这个用户
                            if (sp_compare_password($password, $result['user_pass'])) {//本应用已经有这个用户,且密码正确，同步用户
                                $uc_user_edit_status=uc_user_edit($username, "", $password, "", 1);
                                if ($uc_user_edit_status<=0) {
                                    $this->error("登录错误！");
                                }
                                list($uc_uid2)=uc_get_user($username);
                                $uc_uid=$uc_uid2;
                                $ucenter_old_user_login=true;
                            } else {
                                $this->error("密码错误！");
                            }
                        } else {
                            $this->error("密码错误！");
                        }
                         
                        break;
                }
            }
            $ucenter_login_ok=true;
            echo uc_user_synlogin($uc_uid);
        }
        //exit();
        if (!empty($result)) {
            if (sp_compare_password($password, $result['user_pass'])|| $ucenter_login_ok) {
                session('user', $result);
                //写入此次登录信息
                $data = array(
                    'last_login_time' => date("Y-m-d H:i:s"),
                    'last_login_ip' => get_client_ip(0, true),
                );
                $users_model->where("id=".$result["id"])->save($data);

                $redirect=__ROOT__."/";
                session('login_http_referer', '');
                $ucenter_old_user_login_msg="";
        
                /*if ($ucenter_old_user_login) {
                    // $ucenter_old_user_login_msg="老用户请在跳转后，再次登陆";
                }*/
        
                $this->success("登录验证成功！", $redirect);
            } else {
                $this->error("密码错误！");
            }
        } else {
            $this->error("用户名不存在或已被拉黑！");
        }
    }

    public function logout()
    {
        $ucenter_syn=C("UCENTER_ENABLED");
    	$login_success=false;
    	if($ucenter_syn){
    		include UC_CLIENT_ROOT."client.php";
    		echo uc_user_synlogout();
    	}
    	session("user",null);//只有前台用户退出
    	redirect(__ROOT__."/");
    }

    public function edit()
    {
        if (empty($_POST)) {
            $usrinfo = $_SESSION['user'];

            $this->assign('info', $usrinfo);
            $this->display(":myedit");
        } else {
            $usr_id = $_SESSION['user']['id'];
            $usr = M("users");
            $usr->id = $usr_id;
            $usr->sex = I('post.sex');
            $usr->user_nicename = I('post.nickname');
            $usr->much = I('post.much');
            $usr->hair = I('post.hair');
            $usr->skin = I('post.skin');
            $usr->age = I('post.age');
            $usr->addr = I('post.addr');
            $usr->tel = I('post.tel');
            $usr->birthday = I('post.age');
            $usr->signature = I('post.signature');
            if ($usr->save()) {
                $this->success("修改成功，请重新登录", U("User/logout"));
            } else {
                $this->error("修改失败，请重试");
            }
        }
    }

    public function submit()
    {
        if (empty($_POST)) {
            $id = I("get.id");
            $tid = M("comments")->field("id,post_id,uid")->where("id = {$id}")->select();

            $this->assign("id",$id);
            $this->assign("tid",$tid);
            $this->display(":submitReport");
        }else {
            $reportModel = M("report");
            $reportModel->pid = I("post.pid");
            $reportModel->content = I("post.content");
            $reportModel->uid = session("USR.uid");
            $reportModel->time = time();
            $result = $reportModel->add();
            if($result)    $this->success("提交成功",U('User/index'));
            if(!$result)    $this->error("提交失败，请稍后重试");
        }
    }
}
