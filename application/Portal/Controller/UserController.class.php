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
class UserController extends HomebaseController{

    /**
     * UserController index()
     */
    public function index() {
        $this->display(":my");
    }

    public function register() {
        if (empty($_POST))    return $this->display(":zhuce");

        $usrModel = M("usr");
        $usrArray = $usrModel->select();

        $data['usr'] = I("post.usr");
        $pwd = I("post.psw");
        $repsw = I("post.repsw");
        $data['mail'] = I("post.mail");
        $verify = I("post.verify");

        foreach ($usrArray as $key => $value) {
            if (in_array($data['usr'],$value)) {
                $relay = true;break;
            }
        }

        if ($relay == true)
            die("<script>alert('用户名已存在');history.go(-1)</script>");

        if ($repsw !== $pwd)
            die("<script>alert('两次密码输入不一致，请重新输入!');history.go(-1)</script>");
        $data['pwd'] = md5($pwd);
        if (!sp_check_verify_code($verify))
            die("<script>alert('验证码错误！');history.go(-1)</script>");

        if ($usrModel->data($data)->add()) {
            echo "<script>alert('注册成功');window.location.href='".U('User/login')."'</script>";
        }else {
            echo "<script>alert('注册失败，请过会儿重试');history.go(-1);</script>";
        }

    }

    public function login() {
        if (empty($_POST))    return $this->display(":login");
        $usrModel = M("usr");
        $usr = I("post.usr");
        $pwd = I("post.pwd");
        $usrInfo = $usrModel->where("usr='{$usr}'")->find();
        if (!$usrInfo)
            die("<script>alert('用户名不存在');history.go(-1);</script>");
        if ($usrInfo['pwd'] != md5($pwd))
            die("<script>alert('密码不正确');history.go(-1);</script>");

        $_SESSION['USR'] = $usrInfo;
        die("<script>alert('登录成功');window.location.href='".U('Index/index')."'</script>");
    }

    public function logout(){
    	session('USR',null);
    	$this->redirect(__ROOT__."/");
    }

}
