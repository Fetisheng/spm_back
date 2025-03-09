<?php


namespace addons\xilufitness\controller;


use app\common\controller\Api;
use think\Config;

/**
 * @ApiSector(控制器基类)
 * @ApiInternal
 * Class BaseController
 * @package addons\xilufitness\controller
 */

class Base extends Api
{

    protected $noNeedRight = '*';

    /**
     * 小程序配置信息
     * @var array
     */
    protected $miniConfig = [];

    /**
     * 品牌id
     * @var int
     */
    protected $brand_id;

    /**
     * 品牌key
     * @var string
     */
    protected $brand_key;

    /**
     * 默认纬度
     * @var string
     */
    protected $lat= '31.231859';

    /**默认经度
     * @var string
     */
    protected $lng = '121.486561';

    /**
     * 支付签名
     * 密钥
     */
    private $pay_key = 'xilufitness_pay_key_8888';


    /**
     * 初始化方法
     */
    protected function _initialize()
    {
        parent::_initialize();
        $header = $this->request->header();
        $this->brand_key = $this->request->server('BRAND_KEY', $this->request->request('brand-key', $header['brand-key'] ?? ''));
        if (empty($this->brand_key)) {
            $this->error('品牌key不能为空');
        }
        $brandModel = new \addons\xilufitness\model\Brand;
        $brand_id = $brandModel->where(['brand_key' => $this->brand_key])->value('id');
        if (empty($brand_id)) {
            $this->error('品牌key无效');
        }
        $this->brand_id = empty($brand_id) ? 1 : $brand_id;
        $this->miniConfig = $this->getMiniConfig(intval($this->brand_id));
    }

    /**
     * 支付签名
     * sign 生成
     * @param string $order_id 订单id
     * @param int $order_type 订单类型
     * @param int $timestamp 时间戳
     */
    protected function paySign(string $order_id,int $order_type,int $timestamp){
        return strtoupper(md5($order_id.$this->pay_key.$order_type.$timestamp));
    }

    /**
     * 获取小程序配置
     * @param int $brand_id 品牌商id
     * @return array
     */
    protected function getMiniConfig(int $brand_id){
        $brandConfig = Config::get('xilubrand')[$brand_id] ?? [];
        if(!empty($brandConfig)){
            $this->miniConfig = [
                'appid'          => $brandConfig['mini_appid'] ?? '',
                'appsecret'      => $brandConfig['mini_appsecret'] ?? '',
                'encodingaeskey' => '',
                // 配置商户支付参数
                'mch_id'         => $brandConfig['mini_mch_id'] ?? '',
                'mch_key'        => $brandConfig['mini_mch_key'] ?? '',
                // 配置商户支付双向证书目录 （p12 | key,cert 二选一，两者都配置时p12优先）
                'ssl_p12'        => ROOT_PATH.'public'.$brandConfig['mini_mch_p12'] ?? '',
                'ssl_key'       => ROOT_PATH.'public'.$brandConfig['mini_mch_pem_key'] ?? '',
                'ssl_cer'       => ROOT_PATH.'public'.$brandConfig['mini_mch_pem_cert'] ?? '',
                // 配置缓存目录，需要拥有写权限
                'cache_path'     => RUNTIME_PATH.'wechat',
            ];
            return $this->miniConfig;
        }
        return $brandConfig;

    }



}