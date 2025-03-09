<?php

namespace app\admin\model\xilufitness\card;

use app\admin\model\xilufitness\shop\Index;
use think\Model;

class UserCard extends Model
{
    protected $pk = 'id';
    // 表名
    protected $name = 'xilufitness_user_card';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public function getStatusList(): array
    {
        return ['1' => __('正常'),'2' => __('过期'),'3' => __('退卡'),'4' => __('停卡'), '0' => __('未开卡')];
    }

    public function getPayList(): array
    {
        return ['1' => __('现金支付'),'2' => __('微信支付'),'3' => __('会员卡支付'),'4' => __('其他')];
    }

    /**
     * 关联会员卡类别
     */
    public function category(){
        return $this->belongsTo('\app\admin\model\xilufitness\card\Category','card_category_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联用户
     */
    public function user(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\user\\Index','user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }

    //关联门店
    public function shop(){
        return $this->belongsTo(Index::class,'shop_id','id',[],'LEFT')->setEagerlyType(0);
    }
}