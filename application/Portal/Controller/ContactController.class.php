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
class ContactController extends HomebaseController{

    /**
     * ContactController index()
     */
    public function index() {
        $this->display(":contact");
    }

}
