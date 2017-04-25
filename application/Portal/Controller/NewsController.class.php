<?php

/**
 * 猫女
 */

namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * NewsController
 * @author Ye Weijie <your.name at your.org>
 */
class NewsController extends HomebaseController{

    /**
     * Description of Index()
     */
    public function index() {
        $this->display(":news");
    }

}
