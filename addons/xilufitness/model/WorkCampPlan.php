<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class WorkCampPlan extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_work_camp_plan';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'day_date',
        'week'
    ];

    public function getDayDateAttr($value,$data){
        $value = $value ? $value : ($data['day_date'] ?? '');
        return !empty($value) && is_numeric($value) ? date('m月d日',$value) : $value;
    }

    public function getWeekAttr($value,$data){
        $week = [0 => '周日',1 => '周一', 2 => '周二', 3 => '周三', 4 => '周四', 5 => '周五', 6 => '周六'];
        $value = $value ? $value : ($data['week'] ?? '');
        return $week[$value] ?? '';
    }

    public function work_camp(){
        return $this->belongsTo("WorkCamp",'work_camp_id');
    }

    public function workCamp(){
        return $this->belongsTo(WorkCamp::class,'work_camp_id','id')->setEagerlyType(0);
    }

}