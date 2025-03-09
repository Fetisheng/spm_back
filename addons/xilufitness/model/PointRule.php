<?php


namespace addons\xilufitness\model;

use think\Model;

class PointRule extends Model
{

    // 表名
    protected $name = 'xilufitness_point_rule';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

}