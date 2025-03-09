<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class Shop extends Model
{
    use BaseModel;
    // 表名
    protected $name = 'xilufitness_shop';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [

    ];

    /**
     * 全局搜索
     */
    public function scopeNormal($query){
        return $query->where('status','normal');
    }

}