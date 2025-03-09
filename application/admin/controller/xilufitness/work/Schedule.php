<?php

namespace app\admin\controller\xilufitness\work;

use addons\xilufitness\model\WorkCourse;
use app\admin\controller\xilufitness\traits\Fitness;
use app\admin\model\xilufitness\shop\Index;
use app\common\controller\Backend;
use think\exception\DbException;
use think\Log;
use think\response\Json;

class Schedule extends Backend
{
    use Fitness;

    protected $noNeedRight = ['get_data','get_shop_list'];

    public function _initialize()
    {
        parent::_initialize();
        $this->assign('fitness_brand_id',$this->getFitnessBrandId());
        $this->assign('fitness_shop_id',$this->getFitnessShopId());
        $this->assign('shop_list',$this->get_shop_list());
    }

    public function get_shop_list()
    {
        $fitness_brand_id = $this->getFitnessBrandId();
        $shop = new Index();
        $shop_list = $shop->field(['id as shop_id','shop_name'])
            ->where(function ($query) use ($fitness_brand_id) {
                if ($fitness_brand_id > 0) {
                    $query->where('brand_id', 'eq', $fitness_brand_id);
                }
            })
            ->where('status', '=', 'normal')
            ->order('id','asc')
            ->select();
        return $shop_list;
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
       return $this->view->fetch();
    }

    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function get_data()
    {
        $fitness_shop_id = $this->getFitnessShopId();
        $fitness_brand_id = $this->getFitnessBrandId();
        $shop_id = $this->request->param('shop_id/d',$fitness_shop_id);
        $begin_date = $this->request->param('begin_date/s');
        //获取本周第一天
        if (empty($begin_date)) {
            $start_date = strtotime("Monday this week");
        }else{
            $start_date = strtotime($begin_date);
        }
        //开始日期加7天
        $end_date = strtotime("+7 day",$start_date);
        $work_course = new WorkCourse();
        $where['work_course.status'] = ['in',['normal','complete']];
        $course_list = $work_course
                ->with(['course' => function($query){
                    return $query->withField(['title']);
                }])
                ->field(['work_course.id as work_course_id ','class_time','start_at','end_at'])
                ->where($where)
                ->where('work_course.start_at','>=',$start_date)
                ->where('work_course.start_at','<',$end_date)
                ->where(function ($query) use($fitness_brand_id,$shop_id){
                    if($fitness_brand_id > 0){
                        $query->where('work_course.brand_id','eq',$fitness_brand_id);
                    }
                    if($shop_id > 0){
                        $query->where('work_course.shop_id','eq',$shop_id);
                    }
                })
                ->select();
        // 创建一个空的矩阵数组，用来存放8:00~22:00,周一~周五的时间段
        $time_matrix = [];

        for ($day = 1; $day <= 7; $day++) {
            $item=array();
            for ($hour = 8; $hour < 22; $hour++) {
                $item[] = '';
            }
            $time_matrix[] = $item;
        }

        // 遍历排课列表，将课程时间段填充到矩阵数组中对应的位置
        foreach ($course_list as $course) {

            $class_time = $course['class_time'];
            $index_day = abs($class_time - $start_date) / 86400;
            $time1 = '8:00';
            $time2 = $course['start_at'];
            list($hours1, $minutes1) = explode(':', $time1);
            list($hours2, $minutes2) = explode(':', $time2);
            $index_hour = $hours2 - $hours1;

            // 如果课程开始时间小于8:00，那么将课程开始时间设置为8:00
            $index_hour = max($index_hour, 0);
            // 如果课程结束时间大于22:00，那么将课程结束时间设置为22:00
            $index_hour = min($index_hour, 13);
            $start_hour = $course['start_at'];
            $end_hour = $course['end_at'];
            $title = $course['course']['title'] . ' ' . $start_hour . '~' . $end_hour;
            $content['title'] = $title;
            $content['work_course_id'] = $course['work_course_id'];
            $content['class_time'] = $course['class_time'];
            $content['start_at'] = $course['start_at'];
            $content['end_at'] = $course['end_at'];
            Log::log($content);
            if (is_array($time_matrix[$index_day][$index_hour])) {
                $time_matrix[$index_day][$index_hour][] = $content;
            }else{
                $time_matrix[$index_day][$index_hour] = array();
                $time_matrix[$index_day][$index_hour][] = $content;
            }
        }
        $result = $time_matrix;
        return json(['code' => 1, 'data' => $result]);
    }

}