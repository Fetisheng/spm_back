<?php


namespace addons\xilufitness\controller;

use addons\xilufitness\services\index\IndexService;
use think\Config;

/**
 * @ApiSector(登录注册控制器)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Home extends Base
{
    protected $noNeedLogin = '*';

    /**
     * @ApiTitle(首页数据获取)
     * @ApiSummary(轮播banner数据，门店数据，课程数据)
     * @ApiRoute(addons/xilufitness/home/index)
     * @ApiMethod(GET)
     * @ApiParams(name="lat",type="string",required=true,description="纬度")
     * @ApiParams(name="lng",type="string",required=true,description="经度")
     * @ApiParams(name="city_id",type="string",required=false,description="市id")
     * @ApiParams(name="province_id",type="string",required=false,description="省份id")
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
        $lat                = $this->request->param('lat/s',$this->lat);
        $lng                = $this->request->param('lng/s',$this->lng);
        $city_id            = $this->request->param('city_id/s',0,'xilufitness_get_id_value');
        $province_id        = $this->request->param('province_id/s',0,'xilufitness_get_id_value');
        $data               = IndexService::getInstance()->getHomeData($lat,$lng,$city_id,$province_id);
        $this->success('',$data);
    }

    /**
     * @ApiTitle(获取城市数据)
     * @ApiSummary(获取城市数据列表)
     * @ApiRoute(addons/xilufitness/home/getCityList)
     * @ApiMethod(GET)
     * @ApiParams(name="pid",type="string",required=true,description="父级id")
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
    public function getCityList(){
        $pid = $this->request->param('pid',0,'xilufitness_get_id_value');
        $result = IndexService::getInstance()->getCityList($pid);
        $this->success('',$result);
    }
    /**
     * @ApiTitle(判断ios会员权益是否显示)
     * @ApiSummary(判断ios会员权益是否显示)
     * @ApiRoute(addons/xilufitness/home/getIosInfo)
     * @ApiMethod(GET)
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
    public function getIosInfo(){
        $brand_id = $this->brand_id;
        $brandConfig = Config::get('xilubrand')[$brand_id] ?? [];
        $this->success('',['show_member' => $brandConfig['mini_ios_switch'] ?? 0]);
    }

}