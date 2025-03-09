<?php

namespace addons\xilufitness\model;

use addons\xilufitness\traits\BaseModel;
use think\Model;

class CardPackage extends Model
{
    use BaseModel;
    // 表名
    protected $name = 'xilufitness_card_package';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    /**
     * 关联会员卡套餐详情
     */
    public function details(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(PackageDetails::class,'id','card_package_id',[],'LEFT')->setEagerlyType(0);
    }

}