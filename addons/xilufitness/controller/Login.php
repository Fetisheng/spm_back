<?php


namespace addons\xilufitness\controller;


use addons\xilufitness\services\login\LoginService;
use think\Db;
use think\Log;

/**
 * @ApiSector(登录注册控制器)
 * @ApiRoute(addons/xilufitness/login)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Login extends Base
{

    protected $noNeedLogin = '*';

    /**
     * @ApiTitle(获取小程序openid信息)
     * @ApiSummary(根据前端code换取openid信息)
     * @ApiRoute(/getOpenid)
     * @ApiMethod(GET)
     * @ApiParams(name="code",type="string",required=true,description="前端code值")
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

    public function getOpenid(){
        $code = $this->request->param('code/s');
        try {
            $result = LoginService::getInstance(['mini_config' => $this->miniConfig])->getOpenid($code);
        } catch (\WeChat\Exceptions\LocalCacheException $e){
            $this->error($e->getMessage());
        } catch (\Exception $e){
            $this->error($e->getMessage());
        }
        $this->success('',$result);
    }

    /**
     * @ApiTitle(加密信息解密)
     * @ApiSummary(解密微信信息)
     * @ApiRoute(/decodeData)
     * @ApiMethod(POST)
     * @ApiParams(name = "iv", type = "string",required=true)
     * @ApiParams(name = "encryptedData", type = "string",required=true)
     * @ApiParams(name = "sessionKey", type = "string",required=true)
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function decodeData() {
        $iv             = $this->request->param('iv/s');
        $encryptedData  = $this->request->param('encryptedData/s');
        $sessionKey     = $this->request->param('sessionKey/s');
        if(empty($iv) || empty($encryptedData) || empty($sessionKey)){
            $this->error(__('Params error'));
        }
        $result = LoginService::getInstance(['mini_config' => $this->miniConfig])->decodeData($iv,$sessionKey,$encryptedData);
        $this->success('',$result);
    }

    /**
     * @ApiTitle(注册登录)
     * @ApiSummary(用户注册登录)
     * @ApiRoute(/registerLogin)
     * @ApiMethod(POST)
     * @ApiParams(name=mobile, type=string,required=true,description=手机号)
     * @ApiParams(name = openid, type = string, require = true, description = 小程序openid),
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiReturn({
            'code' => 1,
            'msg' => '获取成功',
            'data' => {}
     *})
     */
    public function registerLogin(){
        $mobile = $this->request->param('mobile/s','');
        $openid = $this->request->param('openid/s','');
        $rec_user_id = $this->request->param('rec_user_id',0,'xilufitness_get_id_value');
        $brand_id = $this->brand_id ?? 0;
        $validateResult = $this->validate(['mobile' => $mobile, 'openid' => $openid],
            '\\addons\\xilufitness\\validate\\UserAccount');
        if(true !== $validateResult){
            Log::log("校验不通过！");
            $this->error($validateResult);
        }
        try {
            Db::startTrans();
            $this->auth->keeptime(time() + 86400*365);
            $result = LoginService::getInstance()->userRegisterLogin($mobile,$openid,$brand_id,$rec_user_id);
            Db::commit();
        } catch (\Exception $e){
            Db::rollback();
            Log::log($e->getMessage());
            $this->error($e->getMessage());
        }
        $this->success('',$result);
    }

}