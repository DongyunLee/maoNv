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
        // $term_id = 14;
        // $posts = sp_sql_posts("cid:{$term_id}");
        $now = time();
        $Report = M("report");
        $data1['date'] = ['egt',$now]; 
        $data2['date'] = ['elt',$now]; 
        $post_now = M("product")->where($data1)->select();
        foreach ($post_now as $key => $value) {
            $post_now[$key]['r_num'] = $Report->where("pid = ".$value['pid'])->count();
        }
        $post_old = M("product")->where($data2)->select();
        foreach ($post_old as $key => $value) {
            $post_old[$key]['r_num'] = $Report->where("pid = ".$value['pid'])->count();
        }

        $this->assign("posts_now",$post_now);
        $this->assign("posts_old",$post_old);
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
