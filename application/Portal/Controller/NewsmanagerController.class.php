<?php

/**
 * 猫女
 */

namespace Portal\Controller;

use Common\Controller\AdminbaseController;

/**
 * NewsmanagerController
 * @author Ye Weijie;
 */
class NewsmanagerController extends AdminbaseController {

    protected $posts_model;
    protected $term_relationships_model;
    protected $terms_model;

    function _initialize() {
        parent::_initialize();
        $this->posts_model = D("Portal/Posts");
        $this->terms_model = D("Portal/Terms");
        $this->term_relationships_model = D("Portal/TermRelationships");
    }

    /**
     * NewsmanagerController index()
     * 后台新闻资讯管理
     */
    public function index() {
        $this->_lists(array("post_status" => array('neq', 3)));
        // $this->_getTree();
        $this->display("AdminPost/index");
    }

    /**
     * 文章列表处理方法,根据不同条件显示不同的列表
     * @param array $where 查询条件
     */
    private function _lists($where = array()) {
        $term_id = 3;

        $where['post_type'] = array(array('eq', 1), array('exp', 'IS NULL'), 'OR');

        if (!empty($term_id)) {
            $where['b.term_id'] = $term_id;
//            $term = $this->terms_model->where(array('term_id' => $term_id))->find();
//            $this->assign("term", $term);
        }

        $start_time = I('request.start_time');
        if (!empty($start_time)) {
            $where['post_date'] = array(
                array('EGT', $start_time)
            );
        }

        $end_time = I('request.end_time');
        if (!empty($end_time)) {
            if (empty($where['post_date'])) {
                $where['post_date'] = array();
            }
            array_push($where['post_date'], array('ELT', $end_time));
        }

        $keyword = I('request.keyword');
        if (!empty($keyword)) {
            $where['post_title'] = array('like', "%$keyword%");
        }

        $this->posts_model
                ->alias("a")
                ->where($where);

        if (!empty($term_id)) {
            $this->posts_model->join("__TERM_RELATIONSHIPS__ b ON a.id = b.object_id");
        }

        $count = $this->posts_model->count();

        $page = $this->page($count, 20);

        $this->posts_model
                ->alias("a")
                ->join("__USERS__ c ON a.post_author = c.id")
                ->where($where)
                ->limit($page->firstRow, $page->listRows)
                ->order("a.post_date DESC");
        if (empty($term_id)) {
            $this->posts_model->field('a.*,c.user_login,c.user_nicename');
        } else {
            $this->posts_model->field('a.*,c.user_login,c.user_nicename,b.listorder,b.tid');
            $this->posts_model->join("__TERM_RELATIONSHIPS__ b ON a.id = b.object_id");
        }
        $posts = $this->posts_model->select();

        $this->assign("page", $page->show('Admin'));
        $this->assign("formget", array_merge($_GET, $_POST));
        $this->assign("posts", $posts);
    }

    // 获取文章分类树结构 select 形式
    private function _getTree() {
        $term_id = empty($_REQUEST['term']) ? 0 : intval($_REQUEST['term']);
        $result = $this->terms_model->order(array("listorder" => "asc"))->select();

        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
            $r['visit'] = "<a href='#'>访问</a>";
            $r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
            $r['id'] = $r['term_id'];
            $r['parentid'] = $r['parent'];
            $r['selected'] = $term_id == $r['term_id'] ? "selected" : "";
            $array[] = $r;
        }

        $tree->init($array);
        $str = "<option value='\$id' \$selected>\$spacer\$name</option>";
        $taxonomys = $tree->get_tree(0, $str);
        $this->assign("taxonomys", $taxonomys);
    }

}
