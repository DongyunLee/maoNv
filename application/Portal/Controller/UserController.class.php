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
        $usr_id = $usrinfo['uid'];
        $id=$usr_id['uid'];

        $user=M("usr");

        // 获取试用申请
        $notry = M("apply")
        ->join("__PRODUCT__ ON __PRODUCT__.pid=__APPLY__.pid")
        ->where("uid={$usr_id}")
        ->select();
        
        // 获取未提交报告
        $try = M("apply")
        ->join("__PRODUCT__ ON __PRODUCT__.pid=__APPLY__.pid")
        ->where("status = 1 AND uid={$usr_id} AND id IS null")
        ->select();
        
        // 获取报告
        $report = M("report")
        ->join("__PRODUCT__ ON __PRODUCT__.pid=__REPORT__.pid")
        ->where('uid='.$usr_id)
        ->order("id desc")
        ->select();
        
        $this->assign('noentry', $notry);
        $this->assign('report', $report);
        $this->assign('product', $try);
        $this->assign('info', $usrinfo);
        $this->assign("ids", $collects);

        if (empty($user)) {
            $this->error("查无此人！");
        }
        $this->assign('info', $usrinfo);
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

        $usrModel = M("usr");
        $usrArray = $usrModel->select();

        $data['usr'] = I("post.usr");
        $data['pwd'] = I("post.psw");
        $repsw = I("post.repsw");
        $data['mail'] = I("post.mail");
        $verify = I("post.verify");

        $relay = $usrModel->where("usr=\"{$data['usr']}\"")->find();

        if ($relay) {
            die("<script>alert('用户名已存在');history.go(-1)</script>");
        }

        if ($repsw !== $data['pwd']) {
            die("<script>alert('两次密码输入不一致，请重新输入!');history.go(-1)</script>");
        }
        $data['pwd'] = md5($data['pwd']);
        if (!sp_check_verify_code($verify)) {
            die("<script>alert('验证码错误！');history.go(-1)</script>");
        }

        if ($usrModel->data($data)->add()) {
            echo "<script>alert('注册成功');window.location.href='".U('User/login')."'</script>";
        } else {
            echo "<script>alert('注册失败，请重试');history.go(-1);</script>";
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
        if (!$is_usr) {
            return $this->error("用户名不存在！");
        }
        if ($is_usr['pwd'] !== md5($info['pwd'])) {
            return $this->error("密码错误");
        }
        session('USR', $is_usr);
        $this->success("登录成功", session('login_http_referer'));
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
        // $this->success("注销成功！", __ROOT__."/");
        redirect(__ROOT__."/");
    }

    public function edit()
    {
        if (empty($_POST)) {
            $usrinfo = session("USR");
            $uid = $usrinfo['uid'];
            $address = M("usr")->where("uid={$uid}")->getField("addr");
            $address = json_decode($address, true);
            
            $this->assign('info', $usrinfo);
            $this->assign('addr', $address);
            return $this->display(":myedit");
        }
            $usr_id = session("USR")['uid'];
            $usr = M("usr");
            $data = I("post");
        if ($usr->where("uid = {$usr_id}")->save($data)) {
            $this->success("修改成功，请重新登录", U("User/logout"));
        } else {
            $this->error("修改失败，请重试");
        }
    }

    public function sub_addr()
    {
        $data['addr'] = json_encode(I("post"), JSON_UNESCAPED_UNICODE);
        $uid = session("USR")['uid'];
        $result = M("usr")->where("uid = {$uid}")->save($data);
        if ($result !== false) {
            $this->success("修改成功！");
        } else {
            $this->error("修改失败");
        }
    }

    public function submit()
    {
        if (empty($_POST)) {
            $aid = I("get.id");
            $apply = M("apply")
            ->join("__USR__ ON __USR__.uid=__APPLY__.uid")
            ->join("__PRODUCT__ ON __PRODUCT__.pid=__APPLY__.pid")
            ->where("aid = {$aid}")
            ->find();

            $this->assign("posts", $apply);
            $this->display(":submitReport");
        } else {
            $reportModel = M("report");
            $data = I("post");
            $data['time'] = time();
            // dump($data);die;
            $result = $reportModel->add($data);
            if ($result !== false) {
                $apply = M("apply");
                $aid =  I("post.aid");
                $map['id'] = $result;
                $res = $apply->where("aid={$aid}")->save($map);

                if ($res !== false) {
                    $this->success("提交成功", U('User/index'));
                }else{
                    $this->error("提交失败，请稍后重试");
                }
            } else {
                $this->error("提交失败，请稍后重试");
            }
        }
    }
}
