<?php

/**
 * 猫女
 */

namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * NewsController
 * @author Ye Weijie 
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
        $uid = session('USR')['uid'];
        $id = I("get.id");
        $post = sp_sql_post($id);
        $tid = I('get.cid');
        $terms = sp_sql_posts('cid:'.$tid.";order:object_id desc;limit:8");
        if($uid){
            $status = M("usr_collection")->where("object_id={$id} AND uid={$uid}")->find();
            $status = ($status) ? 1 : 0 ;
        }else {
            $status = 0;
        }

        $prev = 9999999;
        $next = 0;
        foreach ($terms as $key => $value) {
            if ($value['object_id'] < $id) {
                if ($next < $value['object_id'])    $next = $value['object_id'];
            }
            if ($value['object_id'] > $id) {
                if ($value['object_id'] < $prev)    $prev = $value['object_id'];
            }
        }
        if ($prev != 9999999)    $this->assign("prev",$prev);
        if ($next)    $this->assign("next",$next);

        $this->assign("cid",$tid);
        $this->assign("posts",$terms);
        $this->assign("post",$post);
        $this->assign("cstatus",$status);
        $this->display(":news-detail");
    }

    public function collect()
    {
        $url = $_SERVER['HTTP_REFERER'];
        $id = I("get.id");
        if (!isset($_SESSION['USR']))    $this->error("请先登录",U('User/login'));
        $usr_id = intval($_SESSION['USR']['uid']);

        $collect = M("usr_collection");
        // var_dump($collect->select());die;
        if($collect->where("object_id={$id} and uid={$usr_id}")->select())
            die("<script>alert('您已经收藏过了');window.location.href='{$url}';</script>");
        $collect->object_id = $id;
        $collect->uid = $usr_id;
        $collect->time = time();
        if($collect->add())
            die("<script>alert('收藏成功');window.location.href='{$url}';</script>");
    }


}
