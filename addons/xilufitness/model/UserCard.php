<?php

namespace addons\xilufitness\model;

use addons\xilufitness\traits\BaseModel;
use think\Model;

class UserCard extends Model
{
    use BaseModel;

    protected $pk = 'id';
    // 表名
    protected $name = 'xilufitness_user_card';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    /**
     * 关联会员卡类别
     */
    public function category(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(CardCategory::class,'card_category_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联用户
     */
    public function user(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联品牌商
     */
    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id','id',[],'LEFT')->setEagerlyType(0);
    }

    //关联门店
    public function shop(){
        return $this->belongsTo(Shop::class,'shop_id','id',[],'LEFT')->setEagerlyType(0);
    }
}