<?php


namespace addons\xilufitness\services;


use app\common\library\Auth;
use think\exception\HttpResponseException;
use think\Response;
use think\Validate;

class BaseService
{

    /**
     * 静态实例
     * @var array
     */
    public static $instance = [];

    /**
     * 扩展数据
     * @var array
     */
    public $data = [];

    /**
     * 用户信息
     * @var null
     */
    public $auth = null;

    /**
     * 用户基本信息
     */
    public $userInfo = null;

    /**
     * 品牌id
     */
    public $brand_id = 0;


    /**
     * 获取静态实例
     */
    public static function getInstance(array $params = []){
        $key = md5(get_called_class() . serialize($params));
        if(isset(self::$instance[$key])){
            return self::$instance[$key];
        }
        return self::$instance[$key] = new static($params);
    }

    /**
     * 构造方法
     */
    private function __construct(array $params = [])
    {
        $this->data = $params;
        $this->auth = Auth::instance();
        $this->__initialize();
        $header = request()->header();
        $brand_key = $header['brand-key'] ?? '';
        $brandModel = new \addons\xilufitness\model\Brand;
        $brand_id = $brandModel->where(['brand_key' => $brand_key ])->value('id');
        $this->brand_id = empty($brand_id) ? 1 : $brand_id;
        $userModel = new \addons\xilufitness\model\User;
        $this->userInfo = $userModel
            ->where(['user_id' => $this->auth->id, 'brand_id' => $this->brand_id])
            ->field(['id','nickname','avatar','gender','mobile','point','account','train_day','train_duration','train_count','is_vip'])
            ->find();
    }

    //默认方法
    protected function __initialize(){

    }

    /**
     * 数据验证
     */
    public function validateData($rule,$message,$data){
        $validate = new Validate($rule,$message);
        if(!$validate->check($data)){
            return $validate->getError();
        } else {
            return true;
        }
    }

    /**
     * 返回数据
     * @param string $msg
     * @param array|Ojbct $data
     * @param int $code 状态码
     */
    public function result($msg='',$data,$code=1,array $header=[]){
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => !empty($data) ? $data : '',
        ];
        $response = Response::create($result,'json')->header($header);
        throw new HttpResponseException($response);
    }

    public function resultError($msg='',$data=[]){
        $this->result($msg,$data,0);
    }
    public function resultSuccess($msg='',$data=[]){
        $this->result($msg,$data,1);
    }

    /**
     * 获取用户真实id
     */
    protected function getUserId(){
        return intval(xilufitness_get_id_value($this->userInfo->id ?? 0));
    }

}