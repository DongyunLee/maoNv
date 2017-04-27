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
        $this->display(":my");
    }
    
    public function register() {
        $this->display(":zhuce");
    }
    
    public function login() {
        $this->display(":login");
    }

}
