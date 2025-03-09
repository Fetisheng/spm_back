<?php

namespace app\admin\controller\xilufitness\coach;

use app\admin\controller\xilufitness\traits\Fitness;
use app\common\controller\Backend;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\response\Json;

/**
 * 教练列管理
 *
 * @icon fa fa-circle-o
 */
class Index extends Backend
{
    use Fitness;
    /**
     * Index模型对象
     * @var \app\admin\model\xilufitness\coach\Index
     */
    protected $model = null;

    /**
     * 开启模型验证
     */
    protected $modelValidate = true;
    protected $modelSceneValidate = true;

    /**
     * 开启关联查询
     */
    protected $relationSearch = true;

    /**
     * 快捷查询字段
     */
    protected $searchFields = 'index.coach_name,index.coach_mobile,brand.brand_name,group.group_name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\coach\Index;
        $this->view->assign("coachSexList", $this->model->getCoachSexList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->assign('fitness_brand_id',$this->getFitnessBrandId());
        $this->assign('fitness_shop_id',$this->getFitnessShopId());
    }

    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $fitness_brand_id = $this->getFitnessBrandId();
        $fitness_shop_id = $this->getFitnessShopId();
        $list = $this->model
            ->with(['brand','group'])
            ->where($where)
            ->where(function ($query) use($fitness_brand_id,$fitness_shop_id){
                if($fitness_brand_id > 0){
                    $query->where('index.brand_id','eq',$fitness_brand_id);
                }
                if($fitness_shop_id > 0){
                    $query->whereRaw("FIND_IN_SET({$fitness_shop_id},`shop_ids`)");
                }
            })
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
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
                $coachValidate = \think\Loader::validate($name);
                $coachValidate->rule([
                    'brand_id' => 'require',
                    'shop_ids' => 'require',
                    'coach_name' => 'require',
                    'coach_mobile' => 'require|unique:xilufitness_coach,coach_mobile^brand_id,' . $row->id
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
