<?php

namespace app\admin\model\xilufitness\coach;

use think\Model;
use traits\model\SoftDelete;

class Withdraw extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'xilufitness_coach_withdraw';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];

    /**
     * 获取状态
     */
    public function getStatusList(){
        return [ 0 => __('Status_0'), 1 => __('Status_1'), 2 => __('Status_2'), 3 => __('Status_3'), 4 => __('Status_4')];
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
