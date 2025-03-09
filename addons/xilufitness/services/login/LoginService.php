<?php


namespace addons\xilufitness\services\login;



use addons\xilufitness\services\BaseService;
use fast\Random;
use think\Log;
use WeMini\Crypt;
use function EasyWeChat\Kernel\Support\get_client_ip;

/**
 * 登录注册服务模块
 * Class LoginService
 * @package addons\services\login
 */
class LoginService extends BaseService
{

    /**
     * 获取小程序openid
     * @param string $code 登录时的code
     * @return array
     */
    public function getOpenid(string $code): array {
       return Crypt::instance($this->data['mini_config'])->session($code);
    }

    /**
     * 加密信息解密
     * @param string $iv
     * @param string $encrypted_data
     * @param string $session_key
     * return bool|array
     */
    public function decodeData(string $iv, string $encrypted_data, string $session_key) {
        return Crypt::instance($this->data['mini_config'])->decode($iv,$encrypted_data,$session_key);
    }

    /**
     * 用户注册登录
     * @param string $mobile 手机号
     * @param string $openid 小程序openid
     * @param int $brand_id 品牌id
     * @param int $rec_user_id 推荐人id
     */
    public function userRegisterLogin(string $mobile, string $openid, int $brand_id = 0, int $rec_user_id): array {
        $nickname = Random::build('alnum',4).rand(0,100);
        $password = Random::numeric(6);
        $loginip = get_client_ip();
        $userAccountModel = new \addons\xilufitness\model\User; //附加用户表
        $userModel = new \app\common\model\User; //主用户表
        $user = $userModel::getByMobile($mobile);
        $userAccount = $userAccountModel->where(['brand_id' => $brand_id, 'mobile' => $mobile])->find();
        //登录成功事件字段信息
        $loginSusscess = [
            'openid' => $openid,
            'brand_id' => $brand_id
        ];
        if($user){
            if($userAccount){
                $userAccount->allowField(true)->save(['loginip' => $loginip]);
                $this->auth->direct($userAccount->user_id);
                $loginSusscess['user_id'] = xilufitness_get_id_value($userAccount->id);
                \think\Hook::listen('xilufitness_user_login_success',$loginSusscess);
                $this->saveUserShare(xilufitness_get_id_value($userAccount->id),$rec_user_id,$this->brand_id);
                return ['token' => $this->auth->getToken()];
            } else {
                $userAccountModel->allowField(true)->save([
                    'nickname' => $nickname,
                    'brand_id' => $brand_id,
                    'user_id'  => $user->id ?? 0,
                    'mobile'   => $mobile,
                    'loginip'  => $loginip
                ]);
                $this->auth->direct($user->id);
                $loginSusscess['user_id'] = xilufitness_get_id_value($userAccountModel->id);
                \think\Hook::listen('xilufitness_user_login_success',$loginSusscess);
                $this->saveUserShare(xilufitness_get_id_value($userAccountModel->id),$rec_user_id,$this->brand_id);
                return ['token' => $this->auth->getToken()];
            }
        } else {
            Log::log("注册");
            $result = $this->auth->register($nickname,$password,'',$mobile);
            if($result){
                $userInfo = $this->auth->getUser();
                if($userAccount){
                    $userAccount->allowField(true)->save(['loginip' => $loginip]);
                    $loginSusscess['user_id'] = xilufitness_get_id_value($userAccount->id);
                    \think\Hook::listen('xilufitness_user_login_success',$loginSusscess);
                    $this->saveUserShare(xilufitness_get_id_value($userAccount->id),$rec_user_id,$this->brand_id);
                    return ['token' => $this->auth->getToken()];
                } else {
                    $userAccountModel->allowField(true)->save([
                        'nickname' => $nickname,
                        'brand_id' => $brand_id,
                        'user_id'  => $userInfo['id'] ?? 0,
                        'mobile'   => $mobile,
                        'loginip'  => $loginip
                    ]);
                    $loginSusscess['user_id'] = xilufitness_get_id_value($userAccountModel->id);
                    \think\Hook::listen('xilufitness_user_login_success',$loginSusscess);
                    $this->saveUserShare(xilufitness_get_id_value($userAccountModel->id),$rec_user_id,$this->brand_id);
                    return ['token' => $this->auth->getToken()];
                }
            } else {
                $this->resultError(__('Registration login failed'));
                return [];
            }
        }
    }

    /**
     * 邀请有礼 推荐用户
     * @param int $user_id 用户id
     * @param int $rec_user_id  推荐人id
     * @param int $brand_id 所属品牌商
     * @return bool
     */
    private function saveUserShare(int $user_id, int $rec_user_id, int $brand_id){
        $params['user_id'] = $user_id;
        $params['rec_user_id'] = $rec_user_id;
        $params['brand_id'] = $brand_id;
        return \think\Hook::listen('xilufitness_user_share',$params);
    }

}