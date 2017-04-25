<?php

/**
 * 猫女
 */

namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * TryController
 * @author Ye Weijie;
 */
class TryController extends HomebaseController{

    /**
     * TryController index()
     */
    public function index() {
        $this->display(":try");
    }

}
