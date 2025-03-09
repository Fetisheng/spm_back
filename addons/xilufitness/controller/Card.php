<?php

namespace addons\xilufitness\controller;

use addons\xilufitness\model\TimesCardShare;
use addons\xilufitness\model\UserCard;
use addons\xilufitness\services\CardService;
use think\exception\DbException;
use think\Log;

/**
 * @ApiSector(会员卡控制器)
 * @ApiRoute(addons/xilufitness/card)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Card extends Base
{

    protected $noNeedRight = '*';
    protected $noNeedLogin = 'getCardTypeList';

    /**
     * @ApiTitle(获取用户会员卡)
     * @ApiSummary(获取用户会员卡)
     * @ApiRoute(/index)
     * @ApiMethod(GET)
     * @ApiParams(name="card_type",type="string", require=false,description="会员卡类别 1:次卡 2:时长卡 3:单次卡 4:课程次卡")
     * @ApiParams(name="user_card_id",type="string", require=false,description="会员卡ID")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function index(){
        $card_type = $this->request->param('card_type/s');
        $result = CardService::getInstance()->getUserCardList($card_type);
        $this->success('',$result);
    }


    /**
     * @ApiTitle(购买会员卡)
     * @ApiSummary(购买会员卡)
     * @ApiRoute(/buyUserCard)
     * @ApiMethod(POST)
     * @ApiParams(name="card_category_id",type="integer", require=true,description="会员卡类别ID")
     * @ApiParams(name="shop_id",type="integer", require=true,description="售卡门店ID")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function buyUserCard()
    {
        $card_category_id = $this->request->param('card_category_id/d');
        $shop_id = $this->request->param('shop_id/s',0,'xilufitness_get_id_value');
        CardService::getInstance()->buyUserCard($card_category_id,$shop_id);
        $this->success('购买成功');
    }


    /**
     * @ApiTitle(购买会员卡套餐)
     * @ApiSummary(购买会员卡套餐)
     * @ApiRoute(/buyUserCardPackage)
     * @ApiMethod(POST)
     * @ApiParams(name="card_package_id",type="integer", require=true,description="会员卡套餐ID")
     * @ApiParams(name="shop_id",type="integer", require=true,description="销售门店ID")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
    "code" => 1,
    "msg" => "获取成功",
    "data" => {}
     *})
     */
    public function buyUserCardPackage()
    {
        $card_package_id = $this->request->param('card_package_id/d');
        $shop_id = $this->request->param('shop_id/s',0,'xilufitness_get_id_value');
        CardService::getInstance()->buyUserCardPackage($card_package_id,$shop_id);
        $this->success('购买成功');
    }

    /**
     * @ApiTitle(获取会员卡核销码)
     * @ApiSummary(获取会员卡核销码)
     * @ApiRoute(/getUserCardCodeInfo)
     * @ApiMethod(GET)
     * @ApiParams(name="user_card_id",type="string",required=true,description="会员卡ID")
     * @ApiParams(name="share_id",type="integer", require=false,description="分享ID")
     * @ApiParams(name="check_type",type="integer", require=true,description="核销码类型 1：会员卡 2：单次卡")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     * @throws DbException
     */
    public function getUserCardCodeInfo(){
        $user_card_id = $this->request->param('user_card_id/s');
        $share_id = $this->request->param('share_id/s');
        $check_type = $this->request->param('check_type/s') ?? 1;
        if ($check_type==1) {
            $user_card = new UserCard();
            $result = $user_card->get($user_card_id);
            $info['user_card_id'] = $result->id;
            $info['brand_id'] = $result->brand_id;
            $info['user_id'] = $result->user_id;
            $info['card_type'] = $result->card_type;
            //失效时间当前时间加30s
            $info['expire_time'] = time() + 30;
        }

        if ($check_type==2) {
            $share_card = new TimesCardShare();
            $result = $share_card->get($share_id);
            $info['share_id'] = $result->id;
            $info['brand_id'] = $this->brand_id;
            $info['user_id'] = $result->to_user_id;
            $info['user_card_id'] = $result->user_card_id;
            $info['card_type'] = 3;
            $info['expire_time'] = time() + 30;
        }

        $qrCode = new \QRcode();
        ob_start(); // 开启输出缓冲
        $errorLevel = "L";
        $size = 10;
        $qrCode::png(json_encode($info), false, $errorLevel, $size);
        $imageString = ob_get_contents(); // 获取缓冲区内容
        ob_end_clean(); // 清空并关闭输出缓冲
        $base64Image = 'data:image/png;base64,' . base64_encode($imageString); // 转换为Base64编码
        return json(['qrcode' => $base64Image]); // 返回Base64编码的图片
    }




    /**
     * @ApiTitle(获取会员卡类别列表)
     * @ApiSummary(获取会员卡类别列表)
     * @ApiRoute(/getCardTypeList)
     * @ApiMethod(GET)
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiParams(name="card_type",type="string", require=false,description="会员卡类别 1:次卡 2:时长卡 4：课程次卡)
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
         "code" => 1,
         "msg" => "获取成功",
         "data" => {}
     *})
    */
    public function getCardTypeList(){
        $card_type = $this->request->param('card_type/s');
        $result = CardService::getInstance()->getCardTypeList($card_type);
        $this->success('',$result);
    }


    /**
     * @ApiTitle(获取会员卡套餐列表)
     * @ApiSummary(获取会员卡套餐列表)
     * @ApiRoute(/getCardPackageList)
     * @ApiMethod(GET)
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
    "code" => 1,
    "msg" => "获取成功",
    "data" => {}
     *})
     */
    public function getCardPackageList(){
        $result = CardService::getInstance()->getCardPackageList();
        $this->success('',$result);
    }

    /**
     * @ApiTitle(获取会员卡类别详细信息)
     * @ApiSummary(获取会员卡类别详细信息)
     * @ApiRoute(/getCardTypeInfo)
     * @ApiMethod(GET)
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiParams(name="card_category_id",type="integer", require=true,description="会员卡类别ID")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function getCardTypeInfo(){
        $card_category_id = $this->request->param('card_category_id/s');
        $result = CardService::getInstance()->getCardTypeInfo($card_category_id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(获取会员卡套餐详细信息)
     * @ApiSummary(获取会员卡套餐详细信息)
     * @ApiRoute(/getCardPackageInfo)
     * @ApiMethod(GET)
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiParams(name="card_package_id",type="integer", require=true,description="会员卡套餐ID")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
    "code" => 1,
    "msg" => "获取成功",
    "data" => {}
     *})
     */
    public function getCardPackageInfo(){
        $card_package_id = $this->request->param('card_package_id/s');
        $result = CardService::getInstance()->getCardPackageInfo($card_package_id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(次卡分享)
     * @ApiSummary(次卡分享)
     * @ApiRoute(/timesCardShare)
     * @ApiMethod(POST)
     * @ApiParams(name="user_card_id",type="integer", require=true,description="次卡ID")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function timesCardShare()
    {
        $user_card_id = $this->request->param('user_card_id/s');
        $result = CardService::getInstance()->timesCardShare($user_card_id);
        $this->success('次卡分享成功',$result);
    }


    /**
     * @ApiTitle(核销会员卡)
     * @ApiSummary(核销会员卡)
     * @ApiRoute(/userCardVerification)
     * @ApiMethod(POST)
     * @ApiParams(name="brand_id",type="integer", require=true,description="商户ID")
     * @ApiParams(name="user_card_id",type="String", require=false,description="会员卡ID")
     * @ApiParams(name="share_id",type="integer", require=false,description="分享ID")
     * @ApiParams(name="user_id",type="integer", require=true,description="用户iD")
     * @ApiParams(name="card_type",type="integer", require=true,description="核销类型 1：次卡 2：时长卡 3：单次卡 4：课程次卡")
     * @ApiParams(name="expire_time",type="integer", require=true,description="失效时间")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function userCardVerification()
    {
        $user_card_id = $this->request->param('user_card_id/s',0,'xilufitness_get_id_value');
        $share_id = $this->request->param('share_id/s');
        $user_id = $this->request->param('user_id/s');
        $card_type = $this->request->param('card_type/s');
        $brand_id = $this->request->param('brand_id/s');
        $expire_time = $this->request->param('expire_time/s');
        if (empty($expire_time) || $expire_time < time()) {
            $this->error('核销失败-二维码已失效');
        }
        if ($brand_id != $this->brand_id) {
            $this->error('二维码无效-商家信息不匹配');
        }
        if (in_array($card_type,[1,2,4])) {
            CardService::getInstance()->userCardVerification($user_card_id,$user_id);
        }else if ($card_type == 3) {
            CardService::getInstance()->onceCardVerification($share_id,$user_id);
        } else {
            $this->error('核销失败');
        }

    }

    /**
     * @ApiTitle(获取核销记录)
     * @ApiSummary(获取核销记录)
     * @ApiRoute(/getCardVerificationRecords)
     * @ApiMethod(GET)
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiParams(name="user_card_id",type="integer", require=false,description="会员卡ID(用户端)")
     * @ApiParams   (name="page_index", type="integer", required=false, description="分页页码")
     * @ApiParams   (name="page_size", type="integer", required=false, description="每页条数")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function getCardVerificationRecords()
    {
        $user_card_id = $this->request->param('user_card_id/s',0,'xilufitness_get_id_value');
        $params['page_index'] = $this->request->post("page_index");
        $params['page_size'] = $this->request->post("page_size");
        $page_index = empty($params['page_index']) ? 1 : $params['page_index'];
        $page_size = empty($params['page_size']) ? 10 : $params['page_size'];
        $result = CardService::getInstance()->getCardVerificationRecords($user_card_id,$page_index,$page_size);
        $this->success('',$result);
    }


    /**
     * @ApiTitle(获取次卡分享记录)
     * @ApiSummary(获取次卡分享记录)
     * @ApiRoute(/getTimesCardShare)
     * @ApiMethod(GET)
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiParams(name="user_card_id",type="string", require=false,description="会员卡ID")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function getTimesCardShare()
    {
        $user_card_id = $this->request->param('user_card_id/s',0,'xilufitness_get_id_value');
        $result = CardService::getInstance()->getTimesCardShare($user_card_id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(领取次卡分享)
     * @ApiSummary(领取次卡分享)
     * @ApiRoute(/receiveTimesCardShare)
     * @ApiMethod(POST)
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiParams(name="share_id",type="integer", require=true,description="分享ID")
     * @ApiReturnParams(name="code",type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg",type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data",type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function receiveTimesCardShare()
    {
        $share_id = $this->request->param('share_id/s');
        CardService::getInstance()->receiveTimesCardShare($share_id);
        $this->success('',[]);
    }


}