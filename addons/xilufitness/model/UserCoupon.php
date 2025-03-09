<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Model;

class UserCoupon extends Model
{
    use BaseModel;
    // 表名
    protected $name = 'xilufitness_user_coupon';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'expire_day',
        'expire_time_txt'
    ];

    public function getExpireDayAttr($value,$data){
        $value = $value ? $value : ($data['expire_time'] ?? time());
        $day = round(($value - time())/86400,0);
        return $day > 0 ? $day : 0;
    }

    public function getExpireTimeTxtAttr($value,$data){
        $value = $value ? $value : ($data['expire_time'] ?? time());
        return date('Y-m-d',$value);
    }

    public static function init()
    {
        self::afterInsert(function ($row){
            $model = new \addons\xilufitness\model\Coupon;
            $info =$model
                ->where(['brand_id' => $row->brand_id, 'id' => $row->coupon_id])
                ->field(['coupon_count','receive_count'])
                ->find();
            $updateData['receive_count'] = $info['receive_count'] + 1;
            if($info['coupon_count'] > 0 && $updateData['receive_count'] == $info['coupon_count']){
                $updateData['status'] = 'hidden';
            }
            $info->save($updateData);
        });
    }

}