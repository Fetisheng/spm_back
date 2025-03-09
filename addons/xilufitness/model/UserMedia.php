<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class UserMedia extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_user_media';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [

    ];
}