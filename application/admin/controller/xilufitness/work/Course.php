<?php

namespace app\admin\controller\xilufitness\work;

use addons\xilufitness\services\pay\PayService;
use app\admin\controller\xilufitness\traits\Fitness;
use app\common\controller\Backend;
use DateTime;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Log;
use think\response\Json;

/**
 * 课程排课
 *
 * @icon fa fa-circle-o
 */
class Course extends Backend
{

    use Fitness;

    /**
     * Course模型对象
     * @var \app\admin\model\xilufitness\work\Course
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
    protected $searchFields = 'courses.title,brand.brand_name,shop.shop_name,coach.coach_name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\work\Course;
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
            $this->assignconfig('course_id',$this->request->param('course_id/d',0));
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
            },'courses' => function($query){
                return $query->withField(['title']);
            },'shop' => function($query){
                return $query->withField(['shop_name']);
            },'coach' => function($query){
                return $query->withField(['coach_name']);
            }])
            ->where($where)
            ->where(function ($query) use($fitness_brand_id,$fitness_shop_id){
                if($fitness_brand_id > 0){
                    $query->where('course.brand_id','eq',$fitness_brand_id);
                }
                if($fitness_shop_id > 0){
                    $query->where('course.shop_id','eq',$fitness_shop_id);
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
     * @throws \Exception
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            $add_flag = $this->request->param('add_flag/d', 0);
            if ($add_flag == 1) {
                Log::log($add_flag);
                $this->assign('add_flag',$add_flag);
            }
            return $this->view->fetch();
        }

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }

        $date_start_at = $this->request->param('date_start_at');
        $date_end_at = $this->request->param('date_end_at');
        $date_type = $this->request->param('date_type');
        if($date_type=="week"){
            $week_items = $this->request->param('week_items/a');
            Log::log($week_items);
            $date_list = $this->generate_dates($date_start_at, $date_end_at, "week", $week_items, null);
            foreach ($date_list as $class_time){
                $this->saveCourseWork($params,$class_time);
            }
        }elseif ($date_type=="month"){
            $day_items = $this->request->param('day_items/a');
            Log::log($day_items);
            $date_list = $this->generate_dates($date_start_at, $date_end_at, "month", null, $day_items);
            foreach ($date_list as $class_time){
                $this->saveCourseWork($params,$class_time);
            }
        }else{
            $this->saveCourseWork($params,$params['class_time']);
        }

        $this->success();
    }

    function saveCourseWork($params,$class_time)
    {
        $coach_ids = [];
        foreach ($params['coach_id'] as $val){
            if(is_numeric($val)){
                $coach_ids[] = $val;
            }
        }
        $saveData = [];
        foreach ($params['shop_id'] as $key => $val){
            $saveData[$key] = [
                'brand_id' => $params['brand_id'],
                'course_id' => $params['course_id'],
                'shop_id' => $val,
                'coach_id' => $coach_ids[$key] ?? '',
                'class_time' => $class_time,
                'start_at' => strtotime($class_time.' '.$params['start_at']),
                'end_at' => strtotime($class_time.' '.$params['end_at']),
                'sign_count' => $params['sign_count'],
                'wait_count' => $params['wait_count'],
                'status' => $params['status'],
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
    }

    /**
     * @throws \Exception
     */
    function generate_dates($date_start_at, $date_end_at, $date_type, $week_items, $day_items): array
    {
        $start_date = new DateTime($date_start_at);
        $end_date = new DateTime($date_end_at);
        $date_list = [];
        if ($date_type == "week") {
            if ($week_items !== null) {
                Log::log("开始转换");
                $item_date = $start_date;
                while ($item_date <= $end_date) {
                    if (in_array($item_date->format('w'), $week_items)) {
                        $date_list[] = $item_date->format('Y-m-d');
                    }
                    $item_date->modify('+1 day');
                }
            }
        } elseif ($date_type == "month") {
            if ($day_items !== null) {
                $item_date = $start_date;
                while ($item_date <= $end_date) {
                    if (in_array($item_date->format('d'), $day_items)) {
                        $date_list[] = $item_date->format('Y-m-d');
                    }
                    $item_date->modify('+1 day');
                }
            }
        }
        Log::log($date_list);
        return $date_list;
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
        if(!empty($params['start_at'])){
            $params['start_at'] = strtotime($params['class_time'] . ' '.$params['start_at']);
        }
        if(!empty($params['end_at'])){
            $params['end_at'] = strtotime($params['class_time'] . ' '.$params['end_at']);
        }
        $result = false;
        $old_num = $row->getData('sign_count');
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $result = $row->allowField(true)->save($params);
            if(false !== $result){
                $this->listenSignCount($old_num,$params['sign_count'],$row['id'],$row['course_type'],$row['brand_id']);
            }
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
     * 检测报名人数变化,人数增加了 释放排队
     * @param int $old_num 原来报名人数
     * @param int $new_num 变化后人数
     * @param int $course_id 排课id
     * @param int $course_type 课程类型
     * @param int $brand_id 所属品牌商
     */
    private function listenSignCount(int $old_num, int $new_num,int $course_id=-1,int $course_type=-1,int $brand_id){
        $release_num = intval(bcsub($new_num,$old_num));
        if($release_num > 0){
            return PayService::getInstance()->orderQueue($release_num,$course_id,$course_type,$brand_id);
        }
        return false;
    }


    /**
     * 排课教练选择
     * @param string $shop_ids 门店ids集合
     */
    public function selectCoach(){
        $this->view->engine->layout(false);
        $shop_ids = $this->request->param('shop_ids/s');
        $course_id = $this->request->param('course_id/d',0);
        $model = new \app\admin\model\xilufitness\shop\Index;
        $courseModel = new \app\admin\model\xilufitness\course\Index;
        $list = $model
            ->where('id','in',$shop_ids)
            ->field(['id','brand_id','shop_name'])
            ->select();
        $courseInfo = $courseModel
            ->where(['id' => $course_id])
            ->field(['id','course_price','write_off_price'])
            ->find();
        $html = $this->view->fetch('coach_list',['list' => $list, 'courseInfo' => $courseInfo]);
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
