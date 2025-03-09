<?php


namespace addons\xilufitness\controller;


use addons\xilufitness\services\CouponService;

class Coupon extends Base
{

    protected $noNeedRight = '*';

    protected $noNeedLogin = ['getInviteList'];


    /**
     * @ApiTitle('优惠券领取列表')
     * @ApiSummary('优惠券领取列表')
     * @ApiRoute('addons/xilufitness/coupon/getList')
     * @ApiMethod('GET')
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function getList(){
        $result = CouponService::getInstance()->getList();
        $this->success('',$result);
    }

    /**
     * @ApiTitle('我的优惠券')
     * @ApiSummary('我的优惠券')
     * @ApiRoute('addons/xilufitness/coupon/getMyCoupon')
     * @ApiMethod('GET')
     * @ApiParams(name='status',type='integer',,require = true, description = '状态')
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function getMyCoupon(){
        $status = $this->request->param('status/d',1);
        $result = CouponService::getInstance()->getMyCoupon($status);
        $this->success('',$result);
    }

    /**
     * @ApiTitle('领取优惠券')
     * @ApiSummary('领取优惠券')
     * @ApiRoute('addons/xilufitness/coupon/getCoupon')
     * @ApiMethod('GET')
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiParams(name='id', type = 'string', require = true, description='优惠券id')
     * @ApiParams(name='is_activity', type = 'integer', require = true, description='是否参与邀请活动')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function getCoupon(){
         $id = $this->request->param('id',0,'xilufitness_get_id_value');
         $is_activity = $this->request->param('is_activity/d',0);
         $result = CouponService::getInstance()->getCoupon($id,$is_activity);
         if($result['code'] == 1){
             $this->success();
         } else {
             $this->error($result['msg'] ?? '领取失败');
         }
    }

    /**
     * @ApiTitle('下单选择优惠券')
     * @ApiSummary('下单选择优惠券')
     * @ApiRoute('addons/xilufitness/coupon/getOrderCoupon')
     * @ApiMethod('GET')
     * @ApiParams(name='total_price',type='float',require=true,description='下单价格')
     * @ApiParams(name='id', type = 'string', require = true, description='优惠券id')
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function getOrderCoupon(){
        $total_price = $this->request->param('total_price',0);
        $result = CouponService::getInstance()->getOrderCoupon($total_price);
        $this->success('',$result);
    }
    /**
     * @ApiTitle('邀请有礼')
     * @ApiSummary('邀请有礼赠送优惠券')
     * @ApiRoute('addons/xilufitness/coupon/getInviteList')
     * @ApiMethod('GET')
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function getInviteList(){
        $result = CouponService::getInstance()->getInviteList();
        $this->success('',$result);
    }

}