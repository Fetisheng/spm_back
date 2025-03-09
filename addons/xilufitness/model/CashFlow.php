<?php


namespace addons\xilufitness\model;

use think\Model;
use traits\model\SoftDelete;

class CashFlow extends Model
{
    use SoftDelete;


    // 表名
    protected $name = 'xilufitness_cashflow';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    /**
     * 支付成功
     * @param object $orderInfo
     * @param int $order_type
     * @param int $pay_time
     */
    public function payAfter(object $orderInfo, int $order_type,int $pay_time, int $pay_type=2){
        $cashFlowInfo = $this->where(['order_no' => $orderInfo['order_no'], 'order_type' => $order_type ])->find();
        if(!empty($cashFlowInfo)){
            return $cashFlowInfo->save(['pay_status' => 1, 'pay_time' => $pay_time, 'pay_type' => $pay_type]);
        }
        return false;
    }
}