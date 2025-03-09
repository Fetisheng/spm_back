<?php


namespace addons\xilufitness\controller;


use addons\xilufitness\services\pay\PayService;
use think\Db;

/**
 * @ApiSector(支付中心)
 * @ApiRoute(addons/xilufitness/pay)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Pay extends Base
{
    protected $noNeedRight = '*';
    protected $noNeedLogin = '*';

    /**
     * @ApiTitle(支付中心)
     * @ApiSummary(支付中心网关)
     * @ApiRoute(/index)
     * @ApiMethod(GET)
     * @ApiParams(name="order_id",type="string",require=true,description="订单id")
     * @ApiParams(name="order_type",type="integer",require=true,description="订单类型")
     * @ApiParams(name="timestamp",type="integer",require=true,description="时间戳")
     * @ApiParams(name="pay_type",type="integer",require=true,description="支付方式")
     * @ApiParams(name="sign",type="string",require=true,description="数据签名")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiReturnParams(name="code", type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg", type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data", type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function index(){
        $order_id = $this->request->param('order_id/s');
        $order_type = $this->request->param('order_type/d',0);
        $timestamp = $this->request->param('timestamp/d');
        $pay_type = $this->request->param('pay_type/d',2);
        $sign = $this->request->param('sign/s');
        $makeSign = $this->paySign($order_id,$order_type,$timestamp);
        $order_id = xilufitness_get_id_value($order_id);
        if(time() > $timestamp){
            $this->error('已超时,请重新提交订单');
        }
        if($sign != $makeSign){
            $this->error('签名错误,数据已篡改!!!');
        }
        $result = PayService::getInstance(['mini_config' => $this->miniConfig])->gateWay($order_id,$order_type,$pay_type);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(支付异步回调)
     * @ApiSummary(支付成功异步回调)
     * @ApiRoute(/notify)
     * @ApiMethod(GET)
     * @ApiParams(name="order_type",type="integer",require=true,description="订单类型")
     * @ApiParams(name="brand_id",type="integer",require=true,description="品牌商id")
     * @ApiReturnParams(name="code", type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg", type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data", type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function notify(){
        $order_type = $this->request->param('order_type/d');
        $this->brand_id = $this->request->param('brand_id/d',0);
        $mini_config = $this->getMiniConfig($this->brand_id ?? 0);
        PayService::getInstance(['mini_config' => $mini_config ])->notify($order_type);
    }
}