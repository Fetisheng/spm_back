<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class User extends Model
{
    use BaseModel;
    // 表名
    protected $name = 'xilufitness_user';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'mobile',
        'train_duration'
    ];

    public function getMobileAttr($value,$data){
        $value = $value ? $value : ($data['mobile'] ?? '');
        return substr($value,0,3).'****'.substr($value,-4,4);
    }

    public function getTrainDurationAttr($value,$data){
        $value = $value ? $value : ($data['train_duration'] ?? 0);
        return round(($value/60),1);
    }

}