<?php


namespace addons\xilufitness\model;


use addons\xilufitness\services\user\PointService;
use addons\xilufitness\services\user\UserService;
use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Log;
use think\Model;
use traits\model\SoftDelete;

class OrderRecharge extends Model
{
    use SoftDelete;
    use BaseModel;


    // 表名
    protected $name = 'xilufitness_order_recharge';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'pay_body',
        'pay_time'
    ];

    public function getPayBodyAttr($value,$data){
        return '会员卡充值';
    }

    public function getPayTimeAttr($value,$data){
        $value = $value ? $value : ($data['pay_time'] ?? '');
        return !empty($value) && is_numeric($value) ? date('m.d H:i') : $value;
    }


}