<?php

/**
 * 后台使用审核
 */
namespace Portal\Controller;

use Common\Controller\AdminbaseController;

class ApplyController extends AdminbaseController
{
    
    protected $apply;

    function _initialize()
    {
        parent::_initialize();
        $this->apply = M("apply")
        ->join("__PRODUCT__ ON __PRODUCT__.pid=__APPLY__.pid")
        ->join("__USR__ ON __USR__.uid=__APPLY__.uid");
    }

    /**
     * 显示申请列表
     *
     * @return void
     */
    public function index()
    {
        $now = time();
        $data['date'] = ['egt',$now];
        $data['num'] = ['gt',0];
        $list = $this->apply->where($data)->select();
        // dump($list);
        foreach ($list as $key => $value) {
            $r_num = $this->apply->where("pid = {$value['pid']} AND status=1")->count();
            $list[$key]['r_num'] = $value['num']-$r_num;
        }

        $this->assign("list", $list);
        $this->display();
    }

    public function delete()
    {
        $id = (I('post.ids')!==null)?implode(',', I('post.ids')):I('get.id');
        $map['pid'] = ['in',$id];
        $this->pro_model->startTrans();
        $result1 = $this->pro_model->where($data)->delete();
        $result2 = $this->report_model->where($data)->delete();
        if ($result1&&$result2) {
            $this->pro_model->commit();
            return $this->success("删除成功！");
        } else {
            $this->pro_model->rollback();
            return $this->error("删除失败");
        }
    }

    public function examine()
    {
        $aid = I("get.aid");
        $condition['status'] = 1;
        $result = M("apply")->where("aid={$aid}")->save($condition);
        if ($result) {
            return $this->success("审核成功！");
        } else {
            return $this->error("审核失败");
        }
    }

    public function notry()
    {
        $aid = I("get.aid");
        $condition['status'] = 2;
        $result = M("apply")->where("aid={$aid}")->save($condition);
        if ($result) {
            return $this->success("驳回成功！");
        } else {
            return $this->error("驳回失败");
        }
    }

    public function user()
    {
        $id = I("get.id");
        $aid = I("get.aid");
        $user = M("usr")->where("uid = {$id}")->find();
        $msg = M("apply")->where("aid = {$aid}")->find();

        $this->assign("msg",$msg['msg']);
        $this->assign("user",$user);
        $this->display();
    }
}
