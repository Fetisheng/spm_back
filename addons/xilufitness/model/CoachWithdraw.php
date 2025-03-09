<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Model;

class CoachWithdraw extends Model
{
    use BaseModel;
    // 表名
    protected $name = 'xilufitness_coach_withdraw';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'createtime_txt'
    ];

    public function getCreatetimeTxtAttr($value,$data){
        $value = $value ? $value : $data['createtime'];
        return !empty($value) && is_numeric($value) ? date('m.d H:i',$value) : $value;
    }

    public static function init()
    {
       self::afterInsert(function ($row){
            if(!empty($row['withdraw_price']) && $row['withdraw_price'] > 0){
                $model = new \addons\xilufitness\model\CoachCash;
                $data['coach_id'] = $row['coach_id'];
                $data['brand_id'] = $row['brand_id'];
                $data['title'] = '余额提现';
                $data['is_type'] = 4;
                $data['data_id'] = xilufitness_get_id_value($row['id']);
                $data['cash_type'] = 2;
                $data['cash_price'] = $row['withdraw_price'];
                $withdrawRow = $model
                    ->where(['coach_id' => $data['coach_id'],'brand_id' => $data['brand_id'], 'is_type' => $data['is_type'],
                        'data_id' => $data['data_id'], 'cash_type' => $data['cash_type'] ])
                    ->field(['id'])
                    ->find();
                if(empty($withdrawRow)){
                    $model->allowField(true)->save($data);
                }
            }
       });
    }

}