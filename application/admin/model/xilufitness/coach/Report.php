<?php

namespace app\admin\model\xilufitness\coach;

use think\Model;


class Report extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_coach_report';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    /**
     * 格式化开始时间
     */
    public function SetStartAtAttr($value,$data){
        $value = $value ? $value : ($data['start_at'] ?? '');
        return !empty($value) ? (is_numeric($value) ? $value : strtotime($value)) : '';
    }

    /**
     * 格式化结束时间
     */
    public function SetEndAtAttr($value,$data){
        $value = $value ? $value : ($data['end_at'] ?? '');
        return !empty($value) ? (is_numeric($value) ? $value : strtotime($value)) : '';
    }

    /**
     * 获取状态
     */
    public function getStatusList(){
        return [0 => __('Status_0'), 1 => __('Status_1'), 2 => __('Status_2')];
    }

    /**
     * 关联品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联教练
     */
    public function coach(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\coach\\Index','coach_id','id',[],'LEFT')->setEagerlyType(0);
    }

    







}
