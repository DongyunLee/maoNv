<?php

/**
 * 猫女
 */

namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * AboutusController
 * @author Ye Weijie;
 */
class AboutusController extends HomebaseController{

    /**
     * AboutusController index()
     */
    public function index() {
        $this->display(":aboutUs");
    }

}
