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
        $apply = M("apply");
        
        $data1['date'] = ['egt',$now];
        $data1['num'] = ['gt',0];
        $data2['date'] = ['lt',$now];
        $data2['num'] = ['elt',0];
        $data2['_logic'] = 'or';
        
        $post_now = M("product")->where($data1)->order('pid desc')->select();
        foreach ($post_now as $key => $value) {
            $post_now[$key]['r_num'] = $apply->where("pid = ".$value['pid'])->count();
        }
        
        // 往期试用
        $post_old = M("product")->where($data2)->order('date desc')->select();
        $r_list = array();
        foreach ($post_old as $key => $value) {
            $post_old[$key]['r_num'] = $apply->where("pid = ".$value['pid'])->count();
            $r_list[$key] = $Report
            ->join('__USR__ ON __USR__.uid=__REPORT__.uid')
            ->join('__PRODUCT__ ON __PRODUCT__.pid=__REPORT__.pid')
            ->where("cg_report.pid=".$value['pid'])
            ->order('id desc')
            ->find();
        }
        
        //试用报告
        $report_list = $Report
            ->join('__USR__ ON __USR__.uid=__REPORT__.uid')
            ->join('__PRODUCT__ ON __PRODUCT__.pid=__REPORT__.pid')
            ->order('time desc')
            ->select();
        
        $this->assign("re", $report_list);
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
        $pid = $report['pid'];
        $report['r_num'] = M("apply")->where("pid={$pid}")->count();

        $this->assign("report", $report);
        $this->display(":pinglun");
    }
    
    public function product()
    {
        $id = I("get.id");
        $pro = M("product")->where("pid={$id}")->find();
        $pro['r_num'] = M("apply")->where("pid={$id}")->count();

        // 申请成功用户
        $ok_user = M("apply")
        ->join("__USR__ ON __USR__.uid=__APPLY__.uid")
        ->where("status=1 AND pid={$id}")
        ->order("modified_time desc")
        #->fetchSql()
        ->getField("nickname", true);
        // echo($ok_user);die;

        // 已申请用户
        $apply_user = M("apply")
        ->join("__USR__ ON __USR__.uid=__APPLY__.uid")
        ->order("create_time desc")
        ->where("pid={$id}")
        ->select();

        $this->assign("pro", $pro);
        $this->assign('ok', $ok_user);
        $this->assign('list', $apply_user);
        $this->display(":chanpin");
    }

    public function apply()
    {
        // 产品信息
        $id = I("get.id");
        $pro = M("product")->where("pid={$id}")->find();
        $pro['r_num'] = M("apply")->where("pid={$id}")->count();

        // 申请成功用户
        $ok_user = M("apply")
        ->join("__USR__ ON __USR__.uid=__APPLY__.uid")
        ->where("status=1 AND pid={$id}")
        ->order("modified_time desc")
        ->getField("nickname", true);

        // 已申请用户
        $apply_user = M("apply")
        ->join("__USR__ ON __USR__.uid=__APPLY__.uid")
        ->order("create_time desc")
        ->where("pid={$id}")
        ->select();

        $this->assign('posts', $pro);
        $this->assign('ok', $ok_user);
        $this->assign('list', $apply_user);
        $this->display(":apply");
    }

    public function apply1()
    {
        $id = I("get.id");
        // dump(session());die;
        if (session('USR') === null) {
            return $this->error("请先登录！", U("User/login"));
        }
        $usr = session('USR');

        $product = M("product")->where("pid={$id}")->getField('name,smeta', ':');
        $pro['name'] = key($product);
        $pro['smeta'] = $product[$pro['name']];

        $isset = M("apply")->where("pid={$id} AND uid={$usr['uid']} AND addr IS NOT null")->find();
        // dump(!$isset);
        if ($isset) {
            return $this->error("您已经申请过了，请耐心等待审核",U('Try/index'));
        }

        $this->assign('usr', $usr);
        $this->assign("id", $id);
        $this->assign("pro", $pro);
        $this->display(":apply1");
    }

    public function apply2()
    {
        $id = I("get.id");
        $apply = M("apply")
        ->join("__USR__ ON __USR__.uid=__APPLY__.uid")
        ->join("__PRODUCT__ ON __PRODUCT__.pid=__APPLY__.pid")
        ->where("aid={$id}")
        ->find();

        $uid = $apply['uid'];
        $addr = M("usr")->where("uid={$uid}")->getField("addr");
        $address = json_decode($addr,true);

        $this->assign("id", $id);
        $this->assign("apply", $apply);
        $this->assign("addr",$address);
        $this->display(":apply2");
    }

    public function sub_reply()
    {
        $apply = I("post");
        $apply['uid'] = session("USR")['uid'];
        $apply['create_time'] = time();
        $apply['modified_time'] = $apply['create_time'];
        // dump($apply);die;
        
        $res = M("apply")
        ->where("pid={$apply['pid']} AND uid={$apply['uid']}")
        ->getField("aid");
        if (!empty($res)) {
            $result = M("apply")->where("aid={$res}")->save($apply);
            if ($result !== false) {
                $aid = $res;
            }
        } else {
            $aid = M("apply")->add($apply);
        }
        
        if ($result === false) {
            $this->error("系统故障，请将问题反馈给我们，或者稍后再试");
        }
        redirect(U("Try/apply2?id={$aid}"));
    }

    public function sub_addr()
    {
        $action = I("post.action");
        $id = I("post.aid");
        // dump(I('post'));die;
        $address = json_encode(I("post"), JSON_UNESCAPED_UNICODE);
        
        $apply = M("apply");
        $apply->aid = $id;
        $apply->addr = $address;
        $result = $apply->save();

        if ($action=='update') {
            $uid = session("USR")['uid'];
            $data['addr'] = $address;
            $res = M("usr")->where("uid={$uid}")->save($data);
            if ($res === false) {
                $this->error("更新到个人中心时失败，请稍后重试");
            }
        }
        if ($result !== false) {
            $this->success("申请成功");
        }else {
            $this->error("申请失败");
        }
        
    }
}
