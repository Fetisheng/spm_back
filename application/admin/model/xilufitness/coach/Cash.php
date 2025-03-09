<?php

namespace app\admin\model\xilufitness\coach;

use think\Model;


class Cash extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_coach_cash';
    
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
     * 获取来源类型
     */
    public function getTypeList(){
        return  [ 1 => __('Type_1'), 2 => __('Type_2'), 3 => __('Type_3'), 4 => __('Type_4')];
    }

    /**
     * 获取资金类型
     */
    public function getCashType(){
        return [1 => __('Add'), 2 => __('Dec')];
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
