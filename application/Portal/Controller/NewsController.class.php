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
        $term_id = 9;
        $posts = sp_sql_posts_paged("cid:{$term_id};order:object_id desc", 7,'{liststart}{list}{listend}');
        $page = I('get.p');

        $this->assign('page',$page);
        $this->assign('posts',$posts);
        $this->display(":news");
    }

    public function detail()
    {
        $id = I("get.id");
        $post = sp_sql_post($id);
        $tid = I('get.cid');
        $terms = sp_sql_posts_bycatid($tid,"order:object_id desc;limit:8");

        $this->assign("posts",$terms);
        $this->assign("post",$post);
        $this->display(":news-detail");
    }


}
