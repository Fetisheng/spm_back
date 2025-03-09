<?php

namespace app\admin\controller\xilufitness\course;

use app\admin\controller\xilufitness\traits\Fitness;
use app\common\controller\Backend;
use fast\Tree;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 课程分类
 *
 * @icon fa fa-circle-o
 */
class Cate extends Backend
{
    use Fitness;
    /**
     * Cate模型对象
     * @var \app\admin\model\xilufitness\course\Cate
     */
    protected $model = null;

    /**
     * 开启模型验证
     */
    protected $modelValidate = true;
    protected $modelSceneValidate = true;
    protected $cateList = [];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\course\Cate;
        $this->view->assign("statusList", $this->model->getStatusList());
        $fitness_brand_id = $this->getFitnessBrandId();
        $tree = Tree::instance();
        $list = $this->model
            ->order('weigh asc,id desc')
            ->where(function ($query) use($fitness_brand_id){
                if($fitness_brand_id > 0){
                    $query->where('brand_id','eq',$fitness_brand_id);
                }
            })
            ->select();
        $tree->init(collection($list)->toArray(), 'pid');
        $this->cateList = $tree->getTreeList($tree->getTreeArray(0),'cate_name');
        $this->assign('fitness_brand_id',$this->getFitnessBrandId());
    }

    /**
     * 查询
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        if ($this->request->isAjax()) {
            $total = count($this->cateList);
            $result = array("total" => $total, "rows" => $this->cateList);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $cateValidate = \think\Loader::validate($name);
                $cateValidate->rule([
                    'brand_id' => 'require',
                    'cate_name' => 'require|unique:xilufitness_course_cate,cate_name^brand_id,'.$row->id
                ]);
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }


    //回收站列表
    public function recyclebin() {
        return;
    }
    //回收站(真实删除或清空)
    public function destroy($ids = null) {
        return;
    }
    //回收站还原
    public function restore($ids = null) {
        return;
    }



}
