<?php


namespace addons\xilufitness\model;


use think\Model;

class CardVerificationRecords extends Model
{
    // 表名
    protected $name = 'xilufitness_card_verification_records';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    /**
     * 关联用户
     */
    public function user(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联用户
     */
    public function adminUser(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(User::class,'admin_user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联会员卡
     */
    public function card(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(UserCard::class,'user_card_id','id',[],'LEFT')->setEagerlyType(0);
    }

    //关联门店
    public function shop(){
        return $this->belongsTo(Shop::class,'shop_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联会员卡类别
     */
    public function category(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo('\app\admin\model\xilufitness\card\Category','card_category_id','id',[],'LEFT')->setEagerlyType(0);
    }
}