<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Model;

class UserAccount extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_user_account';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'createtime'
    ];

    public function getCreatetimeAttr($value,$data){
        $value = $value ? $value : ($data['createtime'] ?? '');
        return !empty($value) && is_numeric($value) ? date('m.d H:i',$value) : $value;
    }

    protected static function init()
    {
        parent::init();
        self::afterInsert(function ($row){
            $userModel = new \addons\xilufitness\model\User;
            $userModel->save(['account' => $row['after_account'], 'is_vip' => 1],['id' => $row['user_id']]);
        });
    }


}