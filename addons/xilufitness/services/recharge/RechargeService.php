<?php


namespace addons\xilufitness\services\recharge;


use addons\xilufitness\services\BaseService;
use think\Db;

class RechargeService extends BaseService
{

    protected $model = null;

    protected function __initialize()
    {
        $this->model = new \addons\xilufitness\model\ActivityRecharge;
    }

    /**
     * 获取充值数据
     * @param int $brand_id 品牌商id
     */
    public function getRechargeList(int $brand_id){
        $list = $this->model
            ->normal()
            ->where(['brand_id' => $brand_id])
            ->field(['id','recharge_amount','account_amount','cut_amount'])
            ->order("recharge_amount asc")
            ->select();
        return ['list' => $list];
    }

    /**
     * 获取充值详情
     * @param int $id 套餐id
     * @return array
     */
    public function getRechargeDetail(int $id){
        $info = $this->model
            ->normal()
            ->where(['brand_id' => $this->brand_id, 'id' => $id])
            ->field(['id','recharge_amount','account_amount','cut_amount'])
            ->find();
        return $info;
    }


}