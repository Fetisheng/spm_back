<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Model;

class CoachCash extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_coach_cash';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'createtime_txt'
    ];

    public static function init(){
        self::afterInsert(function ($row){
            if(!empty($row['cash_price']) && $row['cash_price'] > 0){
                if($row['cash_type'] == 1){
                    Db::name('xilufitness_coach_account')->where(['coach_id' => $row['coach_id']])->setInc('account',$row['cash_price']);
                    Db::name('xilufitness_coach_account')->where(['coach_id' => $row['coach_id']])->setInc('total_account',$row['cash_price']);
                }
                if($row['cash_type'] == 2){
                    Db::name('xilufitness_coach_account')->where(['coach_id' => $row['coach_id']])->setDec('account',$row['cash_price']);
                    if($row['is_type'] == 4){
                        //提现
                        Db::name('xilufitness_coach_account')->where(['coach_id' => $row['coach_id']])->setInc('withdraw_account',$row['cash_price']);
                    }
                }
            }
        });
    }

    public function getCreatetimeTxtAttr($value,$data){
        $value = $value ? $value : $data['createtime'];
        return !empty($value) && is_numeric($value) ? date('m.d H:i',$value) : $value;
    }

}