<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class ActivityRecharge extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_activity_recharge';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    /**
     * 全局查询
     */
    public function scopeNormal($query){
        return $query->where('status','normal');
    }

}