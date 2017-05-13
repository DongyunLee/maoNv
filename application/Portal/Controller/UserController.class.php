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
        // $usrinfo = $_SESSION['USR'];
        // $usr_id = $usrinfo['uid'];
        // $collects = M("usr_collection")->where("uid={$usr_id}")->select();
        //
        // $this->assign('info',$usrinfo);
        // $this->assign("ids",$collects);
        // $this->display(":my");
        $id=I("get.id",0,'intval');

		$users_model=M("Users");

		$user=$users_model->where(array("id"=>$id))->find();

		if(empty($user)){
			$this->error("查无此人！");
		}

		$this->assign($user);
		$this->display(":index");
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
        session_unset();
        session_destroy();
    	$this->redirect(__ROOT__."/");
    }

    public function edit()
    {
        if (empty($_POST)) {
            $usrinfo = $_SESSION['USR'];

            $this->assign('info',$usrinfo);
            $this->display(":myedit");
        }else {
            $usr_id = $_SESSION['USR']['uid'];
            $usr = M("usr");
            $usr -> uid = $usr_id;
            $usr->sex = I('post.sex');
            $usr->nickname = I('post.nickname');
            $usr->much = I('post.much');
            $usr->hair = I('post.hair');
            $usr->skin = I('post.skin');
            $usr->age = I('post.age');
            $usr->signature = I('post.signature');
            if ($usr->save()) {
                $this->success("修改成功，请重新登录",U("User/logout"));
            }else {
                $this->error("修改失败，请重试");
            }
        }


    }

}
