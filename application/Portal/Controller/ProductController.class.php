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
        $termId = 4;
        $termsArray = sp_get_child_terms($termId);
        
        foreach ($termsArray as $key => $value) {
            $term_id = $value['term_id'];
            $tag = 'limit:8;order:post_date desc';
            $article[] = sp_sql_posts_bycatid($term_id, $tag);
        }
        
        $this->assign('terms',$termsArray);
        $this->assign('posts',$article);
        $this->display(":product");
    }
    
    public function details() {
        $id = intval(I('get.id'));
        $posts = sp_sql_post($id);
        
        $this->assign('posts',$posts);
        $this->display(":product-detail");
    }
}
