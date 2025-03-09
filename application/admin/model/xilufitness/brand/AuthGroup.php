<?php


namespace app\admin\model\xilufitness\brand;


use think\Model;

class AuthGroup extends Model
{
    // 表名
    protected $name = 'xilufitness_auth_group';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
    ];

}