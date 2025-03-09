<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Model;

class UserPoint extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_user_point';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected $append = [
        'createtime'
    ];

    protected static function init()
    {
        self::afterInsert(function ($row){
            $userModel = new \addons\xilufitness\model\User;
            $userModel->save(['point' => $row['after_point']],['id' => $row['user_id']]);
        });
    }
    public function getCreatetimeAttr($value,$data){
        $value = $value ? $value : ($data['createtime'] ?? '');
        return !empty($value) && is_numeric($value) ? date('m.d H:i',$value) : $value;
    }

}