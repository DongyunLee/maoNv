<?php

/**
 * 猫女
 */

namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * ContactController
 * @author Ye Weijie;
 */
class ContactController extends HomebaseController {

    /**
     * ContactController index()
     */
    public function index() {
        if (isset($_GET['mail'])){ 
            $mail = I('get.mail');
            $this->assign('mail', $mail);
        }

        $this->display(":contact");
    }

    public function nav() {
        $mail = I("post.email");
        $this->redirect("Contact/index?mail={$mail}");
    }
    
    public function submit() {
        $name = I("post.name");
        $city = I("post.city");
        $mail = I("post.mail");
        $msg  = I("post.msg");
        
        if ($name && $city && $mail && $msg) {
            $this->success('留言成功！');
        }  else {
            $this->error('留言失败！');
        }
    }

}
