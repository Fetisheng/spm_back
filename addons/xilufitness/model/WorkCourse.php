<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use app\common\library\Auth;
use think\Model;

class WorkCourse extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_work_course';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'class_time_txt',
        'class_time_txt2',
        'is_plan',
        'order_count'
    ];

    public function getWeek(){
        $week = [0 => '日', 1 => '一',2 => '二', 3 => '三', 4 => '四', 5 => '五', 6 => '六'];
        return $week;
    }

    public function getClassTimeTxtAttr($value,$data){
        $value = $value ? $value : $data['class_time'] ?? '';
        if(!empty($value) && is_numeric($value)){
            $week = $this->getWeek();
            $week_txt = '星期'.$week[date('w',$value)];
            return  date('m月d日',$value).' '. $week_txt;
        }
        return $value;

    }

    public function getClassTimeTxt2Attr($value,$data){
        $value = $value ? $value : $data['class_time'] ?? '';
        if(!empty($value) && is_numeric($value)){
            return  date('m月d日',$value);
        }
        return $value;

    }

    public function getStartAtAttr($value,$data){
        $value = $value ? $value : $data['start_at'] ?? '';
        return !empty($value) && is_numeric($value) ? date('H:i',$value) : '';
    }

    public function getEndAtAttr($value,$data){
        $value = $value ? $value : $data['end_at'] ?? '';
        return !empty($value) && is_numeric($value) ? date('H:i',$value) : '';
    }

    /**
     * 课程状态
     * return int $is_plan 1 可报名 2 可排队 3 已开始 4 已售罄
     */
    public function getIsPlanAttr($value,$data){
        $course_id = xilufitness_get_id_value($data['id'] ?? 0);
        $order_count = $this->getOrderCount($course_id,[1,2,3],$data['course_type'] ?? -1); //已报名人数
        $wait_order = $this->getOrderCount($course_id,10,$data['course_type'] ?? -1); //排队人数
        $start_at = $data['start_at'] ?? time();
        if(time() >= $start_at){
            return 3;
        } elseif (($order_count >= $data['sign_count'] && $data['sign_count'] > 0) || ($data['sign_count'] == 0 && $data['wait_count'] > 0 && $data['wait_count'] >= $wait_order)){
            return 2;
        } elseif ($wait_order >= $data['wait_count']){
            return 4;
        } else {
            return 1;
        }
    }

    public function getOrderCountAttr($value,$data){
        $course_id = xilufitness_get_id_value($data['id'] ?? 0);
        return $this->getOrderCount($course_id,[1,2,3],$data['course_type'] ?? -1);
    }




    /**
     * 统计已报名人数和订单状态
     * @param int $data_id 排课id
     * @param int|array $order_status 订单状态
     */
    public function getOrderCount(int $data_id,$order_status,$order_type= -1){
        $where['data_id'] = $data_id;
        $where['order_type'] = $order_type;
        if(is_array($order_status)){
            $where['order_status'] = ['in',$order_status];
        } else {
            $where['order_status'] = $order_status;
        }
        $orderModel = new \addons\xilufitness\model\Order;
        $order_list = $orderModel
            ->where($where)
            ->select();
        $order_count = array_sum(array_column($order_list,'goods_num'));
        return $order_count;
    }



    /**
     * 全局搜索
     */
    public function scopeNormal($query){
        return $query->where('work_course.status','normal');
    }

    /**
     * 关联课程
     */
    public function course(){
        return $this->belongsTo('Course','course_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联教练
     */
    public function coach(){
        return $this->belongsTo('Coach','coach_id','id',[],'LEFT')->setEagerlyType(0);
    }
    /**
     * 关联门店
     */
    public function shop(){
        return $this->belongsTo('Shop','shop_id','id',[],'LEFT')->setEagerlyType(0);
    }

}