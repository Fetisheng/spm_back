<?php


namespace addons\xilufitness\controller;


use addons\xilufitness\services\user\UserService;
use think\Db;

/**
 * @ApiSector(我的个人中心控制器)
 * @ApiRoute(addons/xilufitness/user)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class User extends Base
{
    protected $noNeedLogin = '*';
    /**
     * @ApiTitle(个人信息)
     * @ApiSummary(我的个人信息)
     * @ApiRoute(/index)
     * @ApiMethod(GET)
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
        $result = UserService::getInstance()->getUserInfo($this->brand_id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(保存基本信息)
     * @ApiSummary(保存基本信息)
     * @ApiRoute(/saveBaseInfo)
     * @ApiMethod(GET)
     * @ApiParams(name="nickname",type="string",required=true,description="昵称")
     * @ApiParams(name="gender",type="integer",required=true,description="性别")
     * @ApiParams(name="avatar",type="string",required=true,description="头像")
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
    public function saveBaseInfo(){
        $params = $this->request->post();
        try {
            Db::startTrans();
            $result = UserService::getInstance()->saveBaseInfo($params,$this->brand_id);
            Db::commit();
        } catch (\Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }
        if(false !== $result){
            $this->success('保存成功');
        }

    }

    /**
     * @ApiTitle(积分记录)
     * @ApiSummary(用户积分记录)
     * @ApiRoute(/getMyPointList)
     * @ApiMethod(GET)
     * @ApiParams(name="page",type="integer",required=true,description="分页码")
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
    public function getMyPointList(){
        $result = UserService::getInstance()->getMyPointList();
        $this->success('',$result);
    }

    /**
     * @ApiTitle(积分余额记录)
     * @ApiSummary(用户积分余额记录)
     * @ApiRoute(/getMyAccountList)
     * @ApiMethod(GET)
     * @ApiParams(name="page",type="integer",required=true,description="分页码")
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
    public function getMyAccountList(){
        $result = UserService::getInstance()->getMyAccountList();
        $this->success('',$result);
    }


    /**
     * @ApiTitle(收藏)
     * @ApiSummary(收藏门店)
     * @ApiRoute(/collect)
     * @ApiMethod(post)
     * @ApiParams(name="id", type="string", require=true,description="id")
     * @ApiParams(name="is_type", type="string", require=true,description="类型 1门店 2 教练")
     * @ApiParams(name="shop_id", type="string", require=false,description="类型教练上传门店id")
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
    public function collect(){
        $id = $this->request->param('id',0,'xilufitness_get_id_value');
        $is_type = $this->request->param('is_type/d',0);
        $shop_id = $this->request->param('shop_id/d',0,'xilufitness_get_id_value');
        $result = UserService::getInstance()->addCollect($id,$is_type,$shop_id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(我的收藏)
     * @ApiSummary(我的收藏)
     * @ApiRoute(/myCollect)
     * @ApiMethod(post)
     * @ApiParams(name="is_type", type="string", require=true,description="类型 1门店 2 教练")
     * @ApiParams(name="lat", type="string", require=true,description="纬度")
     * @ApiParams(name="lng", type="string", require=true,description="经度")
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
    public function myCollect(){
        $is_type = $this->request->param('is_type/d',1);
        $lat = $this->request->param('lat',$this->lat);
        $lng = $this->request->param('lng',$this->lng);
        $result = UserService::getInstance()->getMyCollect($is_type,$lat,$lng);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(我的勋章)
     * @ApiSummary(我的勋章)
     * @ApiRoute(/getMyMedia)
     * @ApiMethod(get)
     * @ApiParams(name="page", type="integer", require=true,description="分页码")
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
    public function getMyMedia(){
        $result = UserService::getInstance()->getMyMediaList();
        $this->success('',$result);
    }

    /**
     * @ApiTitle(训练排名)
     * @ApiSummary(训练排名)
     * @ApiRoute(/getMyRanking)
     * @ApiMethod(get)
     * @ApiParams(name="page", type="integer", require=true,description="分页码")
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
    public function getMyRanking(){
        $result = UserService::getInstance()->getMyRanking();
        $this->success('',$result);
    }

}