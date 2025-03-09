<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class Coupon extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_activity_coupon';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'expire_time'
    ];

    public function scopeNormal($query){
        return $query->where('status','normal');
    }

    public function getExpireTimeAttr($value,$data){
        $days = $data['expire_day'] ?? 0;
        $expire_time = strtotime("+$days days",time());
        return date('Y-m-d',$expire_time);
    }

}