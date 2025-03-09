<?php

namespace app\admin\model\xilufitness\coach;

use think\Model;


class Account extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_coach_account';
    
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
