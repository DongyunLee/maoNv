<?php

/**
 * 猫女
 */

namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * ModelController
 * @author Ye Weijie;
 */
class ModelController extends HomebaseController{

    /**
     * ModelController index()
     */
    public function index() {
        $this->display(":model");
    }

}
