<?php

namespace app\admin\model\xilufitness\card;

use think\Model;

class CardPackage extends Model
{
    // 表名
    protected $name = 'xilufitness_card_package';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public function getStatusList(): array
    {
        return ['1' => __('启用'), '0' => __('禁用')];
    }

    /**
     * 关联会员卡套餐详情
     */
    public function details(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(PackageDetails::class,'id','card_package_id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }
}