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
        $term = 3;
        $terms = sp_get_child_terms($term);

        $this->assign('terms',$terms);
        $this->display(":model");
    }

    public function detail()
    {
        $this->display(":detail");
    }
}
