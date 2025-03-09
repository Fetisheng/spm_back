<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class Cms extends Model
{

    use BaseModel;

    // 表名
    protected $name = 'xilufitness_cms';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [

    ];

}