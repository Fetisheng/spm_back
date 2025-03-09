<?php


namespace addons\xilufitness\controller;


use addons\xilufitness\services\OrderService;
/**
 * @ApiSector(订单中心)
 * @ApiRoute(addons/xilufitness/order)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Order extends Base
{

    protected $noNeedRight = '*';

    protected $noNeedLogin = ['getCodeInfo'];

    /**
     * @ApiTitle(课程/活动/购买会员卡/充值/下单)
     * @ApiSummary(课程/活动/购买会员卡/充值/下单)
     * @ApiRoute(/createOrder)
     * @ApiMethod(GET)
     * @ApiParams(name="id",type="integer",required=true,description="课程/活动/id")
     * @ApiParams(name="is_type",type="integer",required=true,description="类型 1 团课 2 私教 3 活动 4 购买会员卡 5 购买会员卡套餐 0 充值")
     * @ApiParams(name="num",type="integer",required=true,description="数量")
     * @ApiParams(name="pay_type",type="integer",required=true,description="支付方式 0 免支付 1 余额支付 2 微信支付")
     * @ApiParams(name="user_coupon_id",type="string",required=false,description="优惠券id")
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
    public function createOrder(){
        $id = $this->request->param('id/d',0,'xilufitness_get_id_value');
        $coupon_id = $this->request->param('user_coupon_id/d',0);
        $is_type = $this->request->param('is_type/d',1);
        $pay_type = $this->request->param('pay_type/d',1);
        $num = $this->request->param('num/d',1);
        $result = OrderService::getInstance()->createOrder($id,$is_type,$num,$pay_type,$coupon_id);
        $result['timestamp'] = time() + 60;
        $result['sign'] = $this->paySign($result['order_id'],$result['order_type'],$result['timestamp']);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(订单列表")
     * @ApiSummary(课程/活动/下单)
     * @ApiRoute(/getOrderList)
     * @ApiMethod(GET)
     * @ApiParams(name="order_type",type="integer",required=true,description="类型 1 团课 2 私教 3 活动 4 购买会员卡 5 购买会员卡套餐 0 充值")
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
    public function getOrderList(){
        $order_type = $this->request->param('order_type/d',0);
        $result = OrderService::getInstance()->getOrderList($order_type);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(订单详情)
     * @ApiSummary(订单详情数据)
     * @ApiRoute(/getDetail)
     * @ApiMethod(GET)
     * @ApiParams(name="id",type="string",required=true,description="订单id")
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
    public function getDetail(){
        $id = $this->request->param('id/s',0,'xilufitness_get_id_value');
        $result = OrderService::getInstance()->getOrderDetail($id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(获取核销码)
     * @ApiSummary(获取核销码)
     * @ApiRoute(/getCodeInfo)
     * @ApiMethod(GET)
     * @ApiParams(name="id",type="string",required=true,description="订单id")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function getCodeInfo(){
        $urls = $this->request->url(true);
        $qrCode = new \QRcode();
        ob_clean();
        header('Content-Type: image/png');
        $errorLevel = "L";//定义生成图片宽度和高度;默认为3
        $size = "10";//定义生成内容
        $qrCode::png($urls,false,$errorLevel,$size);
        exit();
    }

    /**
     * @ApiTitle(取消订单)
     * @ApiSummary(取消订单)
     * @ApiRoute(/cancelOrder)
     * @ApiMethod(GET)
     * @ApiParams(name="id",type="string",required=true,description="订单id")
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
    public function cancelOrder(){
        $id = $this->request->param('id',0,'xilufitness_get_id_value');
        $result =  OrderService::getInstance(['mini_config' => $this->miniConfig])->cancelOrder($id ?? 0);
        if($result['code'] == 1){
            $this->success('取消成功');
        } else {
            $this->error($result['msg'] ?? '取消失败');
        }
    }

    /**
     * @ApiTitle(订单核销)
     * @ApiSummary(订单核销)
     * @ApiRoute(/confirmOrder)
     * @ApiMethod(GET)
     * @ApiParams(name="urls",type="string",required=true,description="二维码内容")
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
    public function confirmOrder(){
        $url = $this->request->param('urls','','urldecode');
        preg_match('/\d+/i',$url,$ids);
        if(empty($url)){
            $this->error('二维码内容获取失败');
        }
        if(empty($ids)){
            $this->error('二维码内容解析失败');
        }
        $result = OrderService::getInstance()->confirmOrder($ids[0] ?? 0);
        if($result['code'] == 1){
            $this->success('核销成功');
        } else {
            $this->error('核销失败');
        }

    }

    /**
     * @ApiTitle(订单评论)
     * @ApiSummary(订单评论)
     * @ApiRoute(/commentOrder)
     * @ApiMethod(post)
     * @ApiParams(name="order_id",type="integer",required=true,description="订单id")
     * @ApiParams(name="profession_star",type="integer",required=true,description="专业度")
     * @ApiParams(name="affinity_star",type="integer",required=true,description="亲和力")
     * @ApiParams(name="impression_star",type="integer",required=true,description="印象")
     * @ApiParams(name="content",type="string",required=true,description="评论内容")
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
    public function commentOrder(){
        $order_id = $this->request->param('order_id',0,'xilufitness_get_id_value');
        $profession_star = $this->request->param('profession_star/d',0);
        $affinity_star = $this->request->param('affinity_star/d',0);
        $impression_star = $this->request->param('impression_star/d',0);
        $content = $this->request->param('content/s');
        $result = OrderService::getInstance()->addOrderComment($order_id,$profession_star,$affinity_star,$impression_star,$content);
        if($result['code'] == 1){
            $this->success('评论成功');
        } else {
            $this->error($result['msg'] ?? '评论失败');
        }
    }




}