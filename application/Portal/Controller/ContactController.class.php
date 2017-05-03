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

}
