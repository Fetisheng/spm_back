<?php


namespace addons\xilufitness\controller;

use addons\xilufitness\services\recharge\RechargeService;

/**
 * @ApiSector(会员充值)
 * @ApiRoute(addons/xilufitness/recharge)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Recharge extends Base
{
    protected $noNeedLogin = '*';

    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * @ApiTitle(充值数据)
     * @ApiSummary(会员充值数据列表)
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
        $result = RechargeService::getInstance()->getRechargeList($this->brand_id);
        $this->success('',$result);
    }

}