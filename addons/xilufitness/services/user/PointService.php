<?php


namespace addons\xilufitness\services\user;


use addons\xilufitness\services\BaseService;

class PointService extends BaseService
{

    protected $model = null;

    protected function __initialize()
    {
        $this->model = new \addons\xilufitness\model\PointRule;
    }

    /**
     * 获取积分规则
     * @param int $rule_type 规格类型 1 支付赠送积分 2 签到课程 3 邀请好友 4 解锁勋章
     */
    public function getRuleByType(int $rule_type,int $brand_id=0){
        $info = $this->model
            ->where(['brand_id' => !empty($brand_id) ? $brand_id : $this->brand_id, 'rule_type' => $rule_type, 'status' => 'normal'])
            ->field(['point_type','point_amount','point_ratio'])
            ->find();
        return $info;
    }

    /**
     * 发送积分
     * @param string $title 说明
     * @param int $user_id 用户id
     * @param int $rule_type 积分规则
     * @param string $data_id 关联表的id
     * @param int $point_type 变动类型
     * @param float $pay_amount 支付金额
     */
    public function sendPoint(string $title, int $user_id, int $rule_type, string $data_id,
                               int $point_type = 1, float $pay_amount = 0.00,int $brand_id = 0){
        $pointRuleInfo = $this->getRuleByType($rule_type,$brand_id);
        if(empty($this->userInfo)){
            $userModel = new \addons\xilufitness\model\User;
            $this->userInfo = $userModel
                ->where(['id' => $user_id])
                ->field(['id','nickname','avatar','gender','mobile','point','account','train_day','train_duration','train_count','is_vip'])
                ->find();
        }
        $pointData['title'] = $title;
        $pointData['user_id'] = $user_id;
        $pointData['brand_id'] = !empty($brand_id) ? $brand_id : $this->brand_id;
        $pointData['rule_type'] = $rule_type;
        $pointData['data_id'] = xilufitness_get_id_value($data_id);
        $pointData['point_type'] = $point_type;
        $pointData['before_point'] = $this->userInfo->point ?? 0;
        if(!empty($pointRuleInfo['point_ratio'])){
            $pointData['point'] = bcdiv(bcmul($pay_amount,$pointRuleInfo['point_ratio'],0),100,0);
        } else {
            $pointData['point'] = $pointRuleInfo['point_amount'] ?? 0;
        }
        $pointData['after_point'] = bcadd($pointData['before_point'],$pointData['point'],0);
       return \think\Hook::listen('xilufitness_user_point_change',$pointData);
    }


}