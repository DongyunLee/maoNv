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
        $usrinfo = session('USR');
        dump($usrinfo);
        $usr_id = $usrinfo['uid'];
        // dump($usr_id);die;
        $id=sp_get_current_userid();

        $user=M("usr");

        // 获取试用申请
        $notry = M("apply")
        ->join("__PRODUCT__ ON __PRODUCT__.pid=__APPLY__.pid")
        ->where("uid={$usr_id}")
        ->select();
        
        // 获取未提交报告
        $try = M("apply")->where("status = 1 AND uid={$usr_id} AND id IS null")->select();
        // 获取报告
        $report = M("report")->where('uid='.$usr_id)->order("id desc")->select();

        $this->assign('noentry', $notry);
        $this->assign('report', $report);
        $this->assign('product', $try);
        $this->assign('info', $usrinfo);
        $this->assign("ids", $collects);
        $user=$user->where(array("id"=>$id))->find();

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
        $info = I("post");
        // 用户登录
        if ($info == null) {
            // dump($_SERVER);die;
            $redirect=$_SERVER['HTTP_REFERER'];
            session('login_http_referer', $redirect);
            
            if (session('USR') !== null) { //已经登录时直接跳到首页
                redirect(__ROOT__."/");
            } else {
                return $this->display(":login");
            }
        }
        
        //登录验证
        $users = M("usr");
        $is_usr = $users->where("usr=\"{$info['usr']}\"")->find();
        if(!$is_usr)    return $this->error("用户名不存在！");
        if($is_usr['pwd'] !== md5($info['pwd']))    return $this->error("密码错误");
        session('USR',$is_usr);
        $this->success("登录成功",session('login_http_referer'));
        // dump(session());
    }

    public function logout()
    {
        $ucenter_syn=C("UCENTER_ENABLED");
        $login_success=false;
        if ($ucenter_syn) {
            include UC_CLIENT_ROOT."client.php";
            echo uc_user_synlogout();
        }
        session("USR", null);//只有前台用户退出
        $this->success("注销成功！",__ROOT__."/");
        // redirect();
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

            $this->assign("id", $id);
            $this->assign("tid", $tid);
            $this->display(":submitReport");
        } else {
            $reportModel = M("report");
            $reportModel->pid = I("post.pid");
            $reportModel->content = I("post.content");
            $reportModel->uid = session("USR.uid");
            $reportModel->time = time();
            $result = $reportModel->add();
            if ($result) {
                $this->success("提交成功", U('User/index'));
            }
            if (!$result) {
                $this->error("提交失败，请稍后重试");
            }
        }
    }
}
