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
        $term_id = 14;
        $posts = sp_sql_posts("cid:{$term_id}");

        $this->assign("posts",$posts);
        $this->display(":try");
    }

    public function apply()
    {
        $id = I("get.id");
        $posts = sp_sql_post($id);

        $this->assign('posts',$posts);
        $this->display(":apply");
    }

    public function apply2()
    {
        $id = I("get.id");

        $this->assign("id",$id);
        $this->display(":apply1");
    }

    public function apply1()
    {
        $id = I("get.id");
        $usr = session('USR');

        $this->assign('usr',$usr);
        $this->assign("id",$id);
        $this->display(":apply2");
    }

}
