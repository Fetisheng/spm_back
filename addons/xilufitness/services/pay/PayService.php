<?php


namespace addons\xilufitness\services\pay;


use addons\xilufitness\services\BaseService;
use addons\xilufitness\services\user\UserService;
use think\Db;
use think\Exception;
use function EasyWeChat\Kernel\Support\get_client_ip;

class PayService extends BaseService
{

    /**
     * 支付网关
     * @param int $order_id 订单id
     * @param int $order_type 订单类型 0 充值订单 1 团课 2 私教课 3 活动
     * @param int $pay_type 支付类型 0 免支付 1 会员卡支付 2 微信支付
     */
    public function gateWay(int $order_id, int $order_type,int $pay_type = 2){
        $orderInfo = $this->getOrderInfo($order_id,$order_type);
        if(empty($orderInfo)){
            $this->resultError('订单不存在');
        } else if($orderInfo['pay_status'] == 1){
            $this->resultError('订单已支付');
        } else {
            $cashResult = $this->createCash($orderInfo,$order_type);
            if(false !== $cashResult){
                if($pay_type == 0){
                    $result = $this->freePay($orderInfo);
                } elseif ($pay_type == 1){
                    $result = $this->getMemberCardPay($orderInfo);
                } else {
                    $result = $this->getWechatPay($orderInfo,$order_type);
                }
                return $result;
            } else {
                $this->resultError('支付发起失败');
            }

        }
    }

    /**
     * 创建支付流水记录
     * @param object $orderInfo 订单详情
     * @param int $order_type 订单类型
     */
    private function createCash(object $orderInfo, int $order_type){
        $cashModel = new \addons\xilufitness\model\CashFlow;
        $cashInfo = $cashModel
            ->where(['user_id' => $orderInfo->user_id, 'order_no' => $orderInfo->order_no, 'order_type' => $order_type])
            ->field(['id','user_id','order_no','updatetime','createtime'])
            ->find();
        if(!empty($cashInfo)){
            $result = $cashInfo->save(['updatetime' => time(), 'createtime' => time()]);
        } else {
            $result = $cashModel->save([
                'brand_id' => $orderInfo->brand_id,
                'user_id' => $orderInfo->user_id,
                'order_type' => $order_type,
                'order_no' => $orderInfo->order_no,
                'pay_amount' => $orderInfo->pay_amount,
                'pay_type' => $orderInfo->pay_type,
            ]);
        }
        return $result;
    }

    /**
     * 获取订单详情
     * @param int $order_id 订单id
     * @param int $order_type 订单类型 0 充值订单 1 团课 2 私教课 3 活动
     * @param string $order_no 订单号
     */
    public function getOrderInfo(int $order_id, int $order_type,string $order_no='') {
        $orderModel = new \addons\xilufitness\model\Order;
        if(!empty($this->getUserId())){
            $where['user_id'] = $this->getUserId();
        }
        if(!empty($order_id)){
            $where['id'] = $order_id;
        }
        if(!empty($order_no)){
            $where['order_no'] = $order_no;
        }
        $info = $orderModel
            ->where($where)
            ->find();
        return $info;
    }

    /**
     * 获取用户小程序openid
     */
    public function getOpenid(){
        $model = new \addons\xilufitness\model\UserConnect;
        $openid = $model
            ->where(['brand_id' => $this->brand_id, 'account_user_id' => $this->getUserId()])
            ->value('openid');
        return $openid;
    }

    /**
     * 微信支付发起
     * @param object $orderInfo 订单详情
     * @param int $order_type 订单类型
     */
    public function getWechatPay(object $orderInfo,int $order_type){
        try {
            $test_pay_uids = array_filter(explode(',',config('site.wechar_pay_user_ids')));
            $wechat = \WeChat\Pay::instance($this->data['mini_config']);
            // 4. 组装参数，可以参考官方商户文档
            $options = [
                'body'             => $orderInfo['pay_body'] ?? '健身小程序商品支付',
                'out_trade_no'     => $orderInfo['order_no'],
                'total_fee'        => !empty($test_pay_uids) && in_array($orderInfo['user_id'],$test_pay_uids) ? 1 : $orderInfo['pay_amount']*100,
                'openid'           => $this->getOpenid(),
                'trade_type'       => 'JSAPI',
                'notify_url'       => url('/addons/xilufitness/pay/notify',['order_type' => $order_type, 'brand_id' => $orderInfo['brand_id'] ?? 0],true,true),
                'spbill_create_ip' => get_client_ip(),
            ];
            // 生成预支付码
            $result = $wechat->createOrder($options);
            // 创建JSAPI参数签名
            $options = $wechat->createParamsForJsApi($result['prepay_id']);
            return $options;
        } catch (\Exception $e){
            $this->resultError($e->getMessage());
        }
    }

    /**
     * 会员卡支付
     * @param object $orderInfo 订单详情
     */
    public function getMemberCardPay(object $orderInfo){
        return $orderInfo->payAfter($orderInfo,time(),1);
    }

    /**
     * 免支付
     * @param object $orderInfo 订单详情
     */
    public function freePay(object $orderInfo){
        return $orderInfo->payAfter($orderInfo,time(),0);
    }
    /**
     * 支付异步通知
     */
    public function notify($order_type){
        try {
            $wechat = \WeChat\Pay::instance($this->data['mini_config']);
            // 4. 获取通知参数
            $data = $wechat->getNotify();
//            Db::name('xilufitness_logs')->insertGetId(['content' => json_encode($data)]);
//            $data = json_decode(Db::name('xilufitness_logs')->where(['id' => 37])->value('content'),true);
            $orderInfo = $this->getOrderInfo(0,$order_type,$data['out_trade_no']);
            if ($data['return_code'] === 'SUCCESS' && $data['result_code'] === 'SUCCESS') {
                if(!empty($orderInfo) && $orderInfo['pay_status'] == 0){
                    $orderInfo->payAfter($orderInfo,$data['transaction_id'] ?? '',$orderInfo['pay_type']);
                }
                // 返回接收成功的回复
                ob_clean();
                echo $wechat->getNotifySuccessReply();
            }
        } catch (\Exception $e){
            echo $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * 退款申请
     * @param int $order_id 订单id
     * @param int $brand_id 品牌商id
     * @return bool
     */
    public function refundOrder(int $order_id,int $brand_id = 0){
        $model = new \addons\xilufitness\model\Order;
        $where['id'] = $order_id;
        if(!empty($this->getUserId())){
            $where['user_id'] = $this->getUserId();
        }
        $where['brand_id'] = !empty($brand_id) ? $brand_id : $this->brand_id;
        $orderInfo = $model->where($where)->find();
        $cancel_able_time = $orderInfo['starttime'] - 300;
        if(empty($orderInfo)){
            throw new Exception('订单不存在');
        } else if($cancel_able_time < time()){
            throw new Exception('离开课不到5分钟，暂时不能取消');
        } else if($orderInfo['pay_type'] == 0){
            throw new Exception('免支付订单，暂时不能取消');
        } else {
            if($orderInfo['pay_type'] == 1){
                //会员卡
                return  $this->refundCard($orderInfo);
            } elseif($orderInfo['pay_type'] == 2) {
                //微信支付
                return  $this->refundWechat($orderInfo);
            }
        }
    }

    /**
     * 会员卡原路退回
     */
    private function refundCard($orderInfo){
        $title = $this->getRefundTitle($orderInfo);
        return UserService::getInstance()->userAccountChange($title,$orderInfo->user_id,xilufitness_get_id_value($orderInfo->id),$orderInfo['pay_amount'],1,$orderInfo['order_type']);
    }

    /**
     * 微信原路退回
     */
    private function refundWechat($orderInfo){
        $wechat = \WeChat\Pay::instance($this->data['mini_config']);
        $return_no = 'R'.xilufitness_build_order_no();
        $test_pay_uids = array_filter(explode(',',config('site.wechar_pay_user_ids')));
        // 4. 组装参数，可以参考官方商户文档
        $options = [
            'transaction_id' => $orderInfo['trade_no'],
            'out_refund_no'  => $return_no,
            'total_fee'      => !empty($test_pay_uids) && in_array($orderInfo['user_id'],$test_pay_uids) ? 1 : $orderInfo['pay_amount'] * 100,
            'refund_fee'     => !empty($test_pay_uids) && in_array($orderInfo['user_id'],$test_pay_uids) ? 1 : $orderInfo['pay_amount'] * 100,
        ];
        $result = $wechat->createRefund($options);
        if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
            $orderInfo->allowField(true)->save(['re_no' => $return_no ?? '']);
            return true;
        } else {
            throw new Exception($result['err_code_des'] ?? '微信退款申请失败');
        }

    }

    /**
     * 获取退款标题
     */
    private function getRefundTitle($orderInfo){
        if($orderInfo['order_type'] == 1){
            $title = '团课取消退款:'.$orderInfo['order_no'];
        } elseif ($orderInfo['order_type'] == 2){
            $title = '私教课取消退款:'.$orderInfo['order_no'];
        } else {
            $title = '活动取消退款:'.$orderInfo['order_no'];
        }
        return $title;
    }

    /**
     * 排队释放
     * @param int $num 释放人数
     * @param int $data_id 排课id
     * @param int $course_type 课程类型 1 团课 2 私教课 3 活动
     * @return bool
     */
    public function orderQueue(int $num = 0,int $data_id,int $course_type,int $brand_id=0){
        if($num == 0) return false;
        $model = new \addons\xilufitness\model\Order;
        $queueList = $model
            ->where(['brand_id' => $brand_id, 'data_id' => $data_id, 'order_type' => $course_type, 'order_status' => 10])
            ->order("pay_time asc")
            ->limit($num)
            ->select();
        if(empty($queueList)) return false;
        foreach ($queueList as $key => $val){
            $val->save(['order_status' => 1]);
        }
        return true;
    }


}