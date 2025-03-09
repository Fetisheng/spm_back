<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class UserCollect extends Model
{
    use BaseModel;
    // 表名
    protected $name = 'xilufitness_user_collect';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [

    ];

    //关联门店
    public function shop(){
        return $this->belongsTo(Shop::class,'data_id','id',[],'LEFT')->setEagerlyType(0);
    }

    //关联教练
    public function coach(){
        $this->belongsTo(Coach::class,'data_id','id',[],'LEFT')->setEagerlyType(0);
    }

}