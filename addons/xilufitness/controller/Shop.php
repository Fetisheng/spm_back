<?php


namespace addons\xilufitness\controller;

use addons\xilufitness\services\ShopService;

/**
 * @ApiSector(门店控制器)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Shop extends Base
{
    protected $noNeedLogin = ['index','detail','getCourse','getCoach','getCamp'];

    /**
     * @ApiTitle(门店列表)
     * @ApiSummary(门店列表)
     * @ApiRoute(addons/xilufitness/shop/index)
     * @ApiMethod(GET)
     * @ApiParams(name="page", type="integer", require=true,description="分页码")
     * @ApiParams(name="lat", type="string", require=true,description="纬度")
     * @ApiParams(name="lng", type="string", require=true,description="经度")
     * @ApiParams(name="city_id", type="integer", require=false,description="城市id")
     * @ApiParams(name="keywords", type="integer", require=false,description="搜索关键词")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
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
        $lat = $this->request->param('lat/s',$this->lat);
        $lng = $this->request->param('lng/s',$this->lng);
        $city_id = $this->request->param('city_id',0,'xilufitness_get_id_value');
        $keywords = $this->request->param('keywords/s','');
        $result = ShopService::getInstance()->getShopList($lat,$lng,$city_id,$keywords);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(门店详情)
     * @ApiSummary(门店详情)
     * @ApiRoute(addons/xilufitness/shop/detail)
     * @ApiMethod(GET)
     * @ApiParams(name="id", type="string", require=true,description="门店id")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiReturnParams(name="code", type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg", type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data", type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function detail(){
        $id = $this->request->param('id','','xilufitness_get_id_value');
        $result = ShopService::getInstance()->getDetail($id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(门店团课)
     * @ApiSummary(获取门店团课)
     * @ApiRoute(addons/xilufitness/shop/getCourse)
     * @ApiMethod(GET)
     * @ApiParams(name="id", type="string", require=true,description="门店id")
     * @ApiParams(name="is_type", type="integer", require=true,description="课程类型")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiReturnParams(name="code", type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg", type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data", type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function getCourse(){
        $id = $this->request->param('id/s','','xilufitness_get_id_value');
        $is_type = $this->request->param('is_type/d',1);
        $result = ShopService::getInstance()->getCourse($id,$is_type);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(门店私教)
     * @ApiSummary(获取门店私教)
     * @ApiRoute(addons/xilufitness/shop/getCoach)
     * @ApiMethod(GET)
     * @ApiParams(name="id", type="string", require=true,description="门店id")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiReturnParams(name="code", type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg", type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data", type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function getCoach(){
        $id = $this->request->param('id/s','','xilufitness_get_id_value');
        $result = ShopService::getInstance()->getCoach($id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(门店活动)
     * @ApiSummary(获取门店活动)
     * @ApiRoute(addons/xilufitness/shop/getCamp)
     * @ApiMethod(GET)
     * @ApiParams(name="id", type="string", require=true,description="门店id")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiReturnParams(name="code", type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg", type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data", type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function getCamp(){
        $id = $this->request->param('id/s','','xilufitness_get_id_value');
        $result = ShopService::getInstance()->getCamp($id);
        $this->success('',$result);
    }




}