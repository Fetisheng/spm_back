<?php

namespace addons\xilufitness\model;

use addons\xilufitness\traits\BaseModel;
use think\Model;

class TimesCardShare extends Model
{
    use BaseModel;
    protected $pk = 'id';
    // 表名
    protected $name = 'xilufitness_times_card_share';

    /**
     * 关联用户
     */
    public function toUser(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(User::class,'to_user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联用户
     */
    public function fromUser(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(User::class,'from_user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联会员卡
     */
    public function card(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(UserCard::class,'user_card_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联会员卡类别
     */
    public function category(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(CardCategory::class,'card_category_id','id',[],'LEFT')->setEagerlyType(0);
    }

    //关联门店
    public function shop(){
        return $this->belongsTo(Shop::class,'shop_id','id',[],'LEFT')->setEagerlyType(0);
    }

}