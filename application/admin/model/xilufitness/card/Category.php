<?php

namespace app\admin\model\xilufitness\card;

use think\Model;

class Category extends Model
{
    // 表名
    protected $name = 'xilufitness_card_category';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public function getStatusList(): array
    {
        return ['1' => __('启用'), '0' => __('禁用')];
    }

    /**
     * 关联品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }
}