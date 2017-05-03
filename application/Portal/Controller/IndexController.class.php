<?php

/*
 *      _______ _     _       _     _____ __  __ ______
 *     |__   __| |   (_)     | |   / ____|  \/  |  ____|
 *        | |  | |__  _ _ __ | | _| |    | \  / | |__
 *        | |  | '_ \| | '_ \| |/ / |    | |\/| |  __|
 *        | |  | | | | | | | |   <| |____| |  | | |
 *        |_|  |_| |_|_|_| |_|_|\_\\_____|_|  |_|_|
 */
/*
 *     _________  ___  ___  ___  ________   ___  __    ________  _____ ______   ________
 *    |\___   ___\\  \|\  \|\  \|\   ___  \|\  \|\  \ |\   ____\|\   _ \  _   \|\  _____\
 *    \|___ \  \_\ \  \\\  \ \  \ \  \\ \  \ \  \/  /|\ \  \___|\ \  \\\__\ \  \ \  \__/
 *         \ \  \ \ \   __  \ \  \ \  \\ \  \ \   ___  \ \  \    \ \  \\|__| \  \ \   __\
 *          \ \  \ \ \  \ \  \ \  \ \  \\ \  \ \  \\ \  \ \  \____\ \  \    \ \  \ \  \_|
 *           \ \__\ \ \__\ \__\ \__\ \__\\ \__\ \__\\ \__\ \_______\ \__\    \ \__\ \__\
 *            \|__|  \|__|\|__|\|__|\|__| \|__|\|__| \|__|\|_______|\|__|     \|__|\|__|
 */
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------

namespace Portal\Controller;

use Common\Controller\HomebaseController;

/**
 * 首页
 */
class IndexController extends HomebaseController {

    //首页 小夏是老猫除外最帅的男人了
    public function index() {
        $term_id = '9,3';
        $posts = sp_sql_posts_bycatid($term_id,'order:post_hits desc;limit:5');

        $this->assign('posts', $posts);
        $this->display(":index");
    }

    /**
     * Detail()
     */

    public function detail() {
        $id = I('get.id');
        $post = sp_sql_post($id);
        $posts = sp_sql_posts_bycatid(9,'order:object_id desc');
        $posts_limit = array_slice($posts, 0, 7);
        $prev = 9999999999999;
        $next = 0;
        foreach ($posts as $value) {
            if($id<$value['object_id']){
                if($value['object_id']<$prev){
                    $prev = $value['object_id'];
                    $this->assign('prev',$prev);
                }
            }
            if ($id>$value['object_id']) {
                if($value['object_id']>$next){
                    $next = $value['object_id'];
                    $this->assign('next',$next);
                }
            }
        }

        $this->assign('post',$post);
        $this->assign('posts',$posts_limit);
        $this->display(":news-detail");
    }

}
