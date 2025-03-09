<?php
namespace app\admin\model\xilufitness\card;

use app\admin\model\xilufitness\user\Index;
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
     * 关联品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联用户
     */
    public function user(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(Index::class,'user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联用户
     */
    public function admin(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(Index::class,'admin_user_id','id',[],'LEFT')->setEagerlyType(0);
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
        return $this->belongsTo(\app\admin\model\xilufitness\shop\Index::class,'shop_id','id',[],'LEFT')->setEagerlyType(0);
    }

}