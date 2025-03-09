<?php

namespace addons\xilufitness\model;

use addons\xilufitness\traits\BaseModel;
use think\Model;

class PackageDetails extends Model
{
    use BaseModel;
    // 表名
    protected $name = 'xilufitness_package_details';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

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
}