<?php

/**
 * 猫女
 */

namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * MyeditController
 * @author Ye Weijie;
 */
class MyeditController extends HomebaseController{

    /**
     * MyeditController index()
     */
    public function index() {
        $this->display(":myedit");
    }

}
