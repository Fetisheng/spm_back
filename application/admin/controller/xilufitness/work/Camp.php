<?php

namespace app\admin\controller\xilufitness\work;

use app\admin\controller\xilufitness\traits\Fitness;
use app\common\controller\Backend;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\response\Json;

/**
 * 活动排课
 *
 * @icon fa fa-circle-o
 */
class Camp extends Backend
{
    use Fitness;

    /**
     * Camp模型对象
     * @var \app\admin\model\xilufitness\work\Camp
     */
    protected $model = null;
    protected $noNeedRight = ['selectCoach'];

    /**
     * 开启模型验证
     */
    protected $modelValidate = true;
    protected $modelSceneValidate = true;

    /**
     * 开启
     * 关联查询
     */
    protected $relationSearch = true;

    /**
     * 快捷查询
     */
    protected $searchFields = 'camps.title,brand.brand_name,shop.shop_name,coach.coach_name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\work\Camp;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->assign('fitness_brand_id',$this->getFitnessBrandId());
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
            $this->assignconfig("camp_id",$this->request->param('camp_id/d',0));
            $this->assignconfig('shop_id',$this->request->param('shop_id/d',0));
            $coach_id = $this->request->param('coach_id/d',0);
            $brand_id = $this->request->param('brand_id/d',0);
            $this->assignconfig('coach_id',$coach_id);
            $this->assignconfig('brand_id',$brand_id);
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
            ->with(['brand' => function($query){
                return $query->withField(['brand_name']);
            },'camps' => function($query){
                return $query->withField(['title']);
            },'shop' => function($query){
                return $query->withField(['shop_name']);
            },'coach' => function($query){
                return $query->withField(['coach_name']);
            }])
            ->where($where)
            ->where(function ($query) use($fitness_brand_id,$fitness_shop_id){
                if($fitness_brand_id > 0){
                    $query->where('camp.brand_id','eq',$fitness_brand_id);
                }
                if($fitness_shop_id > 0){
                    $query->where('camp.shop_id','eq',$fitness_shop_id);
                }
            })
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    /**
     * 添加
     *
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        if(empty($params['day_date'])){
            $this->error(__('Please add camp plan'));
        }
        $params = $this->preExcludeFields($params);
        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        $coach_ids = [];
        foreach ($params['coach_id'] as $val){
            if(is_numeric($val)){
                $coach_ids[] = $val;
            }
        }
        $saveData = []; //批量保存数据
        $planData = []; //计划数据
        foreach ($params['day_date'] as $k => $v){
            $planData[$k] = [
                'day_date' => $v,
                'day_start_at' => $params['day_start_at'][$k] ?? '',
                'day_end_at' => $params['day_end_at'][$k] ?? '',
            ];
        }
        $start_at_list = [];
        $end_at_list = [];
        array_walk($planData,function ($val,$key) use(&$start_at_list,&$end_at_list){
            $start_at_list[$key] = strtotime($val['day_date'].' '.$val['day_start_at']);
            $end_at_list[$key] = strtotime($val['day_date'].' '.$val['day_end_at']);
        });
        sort($start_at_list);
        rsort($end_at_list);
        if(!empty($start_at_list)){
            $params['start_at'] = date('Y-m-d H:i:s',$start_at_list[0]);
        }
        if(!empty($end_at_list)){
            $params['end_at'] = date('Y-m-d H:i:s',$end_at_list[0]);
        }
        foreach ($params['shop_id'] as $key => $val){
            $saveData[$key] = [
                'brand_id' => $params['brand_id'],
                'camp_id' => $params['camp_id'],
                'shop_id' => $val,
                'coach_id' => $coach_ids[$key] ?? '',
                'start_at' => $params['start_at'],
                'end_at' => $params['end_at'],
                'class_duration' => $params['class_duration'],
                'sign_count' => $params['sign_count'] ?? 1,
                'camp_count' => $params['camp_count'],
                'total_count' => $params['total_count'],
                'description' => $params['description'],
                'status' => $params['status'],
                'plan_data' => $planData
            ];
        }
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }
            $result = $this->model->allowField(true)->saveAll($saveData);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
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
            $plan_list = $this->model->getPlanList($row['id']);
            $this->view->assign('plan_list',$plan_list);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $planData = []; //计划数据
        foreach ($params['day_date'] as $k => $v){
            $planData[$k] = [
                'day_date' => $v,
                'day_start_at' => $params['day_start_at'][$k] ?? '',
                'day_end_at' => $params['day_end_at'][$k] ?? '',
            ];
            if(!empty($params['plan_id'][$k])){
                $planData[$k]['id'] = $params['plan_id'][$k];
            }
        }
        $start_at_list = [];
        $end_at_list = [];
        array_walk($planData,function ($val,$key) use(&$start_at_list,&$end_at_list){
            $start_at_list[$key] = strtotime($val['day_date'].' '.$val['day_start_at']);
            $end_at_list[$key] = strtotime($val['day_date'].' '.$val['day_end_at']);
        });
        sort($start_at_list);
        rsort($end_at_list);
        if(!empty($start_at_list)){
            $params['start_at'] = date('Y-m-d H:i:s',$start_at_list[0]);
        }
        if(!empty($end_at_list)){
            $params['end_at'] = date('Y-m-d H:i:s',$end_at_list[0]);
        }

        $params['plan_data'] = $planData;
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
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


    /**
     * 排课教练选择
     * @param string $shop_ids 门店ids集合
     * @param int $camp_id 活动id
     */
    public function selectCoach(){
        $this->view->engine->layout(false);
        $shop_ids = $this->request->param('shop_ids/s');
        $camp_id  = $this->request->param('camp_id/d',0);
        $model = new \app\admin\model\xilufitness\shop\Index;
        $campModel = new \app\admin\model\xilufitness\course\Camp;
        $list = $model
            ->where('id','in',$shop_ids)
            ->field(['id','brand_id','shop_name'])
            ->select();
        $campInfo = $campModel
            ->where(['id' => $camp_id])
            ->field(['id','camp_price','write_off_price'])
            ->find();
        $html = $this->view->fetch('coach_list',['list' => $list, 'campInfo' => $campInfo]);
        return json(['code' => 1, 'msg' => '', 'data' => ['html' => !empty($list) ? $html : ''] ]);
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
