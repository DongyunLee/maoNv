<?php

namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * Description of ProductController
 *
 * @author Administrator
 */
class ProductController extends HomebaseController {
    public function index() {
        $this->display(":product");
    }
}
