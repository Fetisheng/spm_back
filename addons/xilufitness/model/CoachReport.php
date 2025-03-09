<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class CoachReport extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_coach_report';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [

    ];

    public function getStartAtAttr($value,$data){
        $value = $value ? $value : ($data['start_at'] ?? '');
        return !empty($value) && is_numeric($value) ? date('Y-m-d H:i') : $value;
    }

    public function getEndAtAttr($value,$data){
        $value = $value ? $value : ($data['end_at'] ?? '');
        return !empty($value) && is_numeric($value) ? date('Y-m-d H:i') : $value;
    }

    public function setStartAtAttr($value,$data){
        $value = $value ? $value : ($data['start_at'] ?? '');
        return !empty($value) && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function setEndAtAttr($value,$data){
        $value = $value ? $value : ($data['end_at'] ?? '');
        return !empty($value) && !is_numeric($value) ? strtotime($value) : $value;
    }

}