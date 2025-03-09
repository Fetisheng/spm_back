<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class ActivityMedia extends Model
{
    use BaseModel;
    // 表名
    protected $name = 'xilufitness_activity_medal';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'description'
    ];

    //全局查询
    public function scopeNormal($query){
        $query->where('status','normal');
    }

    public function getDescriptionAttr($value,$data){
        $value = $value ? $value : $data['description'];
        return array_filter(explode("\r\n",$value));
    }
}