<?php

/**
 * 后台产品管理
 */
namespace Portal\Controller;

use Common\Controller\AdminbaseController;

class ProController extends AdminbaseController
{
    protected $pro_model;
    protected $report_model;
    protected $reportList;

    function _initialize()
    {
        parent::_initialize();
        $this->pro_model = M("product");
        $this->report_model = M("report");
        $this->reportList = M("report");
        // var_dump($this->reportList);die;
        $this->reportList
        ->join('__USR__ ON __USR__.uid=__REPORT__.uid')
        ->join('__PRODUCT__ ON __PRODUCT__.pid=__REPORT__.pid')
        ->select();
    }

    /**
     * 显示产品列表
     *
     * @return void
     */
    public function index()
    {
        $list = $this->pro_model->select();
        foreach ($list as $key => $value) {
           $pid = $value['pid']; 
           $count = $this->report_model->where("pid=\"{$pid}\"")->count();
           $list[$key]['r_num'] = intval($count);
        }
        // dump($list);

        $this->assign("list", $list);
        $this->display();
    }

    /**
     * 添加产品页面
     *
     */
    public function add()
    {
        $this->display();
    }

    /**
     * ajax添加
     *
     * @return void $result
     */
    public function add_post()
    {
        $data = I("post");
        $data['date'] = strtotime($data['date']);
        $result = $this->pro_model->add($data);
        if ($result) {
            $this->success("添加成功！");
        } else {
            $this->error("添加失败，请稍后再试");
        }
        
    }

    public function delete()
    {
        $id = (I('post.ids')!==Null)?implode(',',I('post.ids')):I('get.id');
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

    public function edit()
    {
        echo 2;
    }
    
    public function menu()
    {
        $this->display();
    }
}
