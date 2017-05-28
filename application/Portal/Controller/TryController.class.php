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
class TryController extends HomebaseController
{

    /**
     * TryController index()
     */
    public function index()
    {
        // 正在试用
        $now = time();
        $Report = M("report");
        
        $data1['date'] = ['egt',$now];
        $data2['date'] = ['elt',$now];
        $post_now = M("product")->where($data1)->order('pid desc')->select();
        foreach ($post_now as $key => $value) {
            $post_now[$key]['r_num'] = $Report->where("cg_report.pid = ".$value['pid'])->count();
        }
        // 往期试用
        $post_old = M("product")->where($data2)->order('pid desc')->select();
        $r_list = array();
        foreach ($post_old as $key => $value) {
            $post_old[$key]['r_num'] = $Report->where("cg_report.pid = ".$value['pid'])->count();
            $r_list[$key] = $Report
            ->join('__USR__ ON __USR__.uid=__REPORT__.uid')
            ->join('__PRODUCT__ ON __PRODUCT__.pid=__REPORT__.pid')
            ->where("cg_report.pid=".$value['pid'])
            ->order('id desc')
            ->find();
        }

        $this->assign("r_list", $r_list);
        $this->assign("posts_now", $post_now);
        $this->assign("posts_old", $post_old);
        $this->display(":try");
    }

    public function reply()
    {
        $id = I("get.id");
        $report = M("report")
        ->join("__USR__ ON __USR__.uid=__REPORT__.uid")
        ->join("__PRODUCT__ ON __PRODUCT__.pid=__REPORT__.pid")
        ->where("cg_report.id={$id}")
        ->find();
        $report['r_num'] = M("report")->
        $this->assign("report",$report);
        $this->display(":pinglun");
    }

    public function apply()
    {
        $id = I("get.id");
        $posts = sp_sql_post($id);

        $this->assign('posts', $posts);
        $this->display(":apply");
    }

    public function apply2()
    {
        $id = I("get.id");

        $this->assign("id", $id);
        $this->display(":apply1");
    }

    public function apply1()
    {
        $id = I("get.id");
        $usr = session('USR');

        $this->assign('usr', $usr);
        $this->assign("id", $id);
        $this->display(":apply2");
    }
}
