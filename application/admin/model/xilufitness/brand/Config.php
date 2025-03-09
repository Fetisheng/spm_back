<?php


namespace app\admin\model\xilufitness\brand;


use think\Model;

class Config extends Model
{
    // 表名
    protected $name = 'xilufitness_brand_config';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
    ];

    /**
     * 获取配置栏目
     * @param int $brand_id 品牌id
     * @return array $list
     */
    public function getNavBar(int $brand_id): array {
        $navBar = $this->where(['brand_id' => 0, 'name' => 'configgroup'])->value('value');
        $list = [];
        if(!empty($navBar)){
            $navBar = json_decode($navBar,true);
            if(empty($navBar)){
                $navBar = ['mini' => 'Mini'];
            }
            foreach ($navBar as $key => $value) {
                $settingList = $this->getDefaultSettingByData($brand_id,$key);
                if(empty($settingList)){
                    $settingList = $this->getDefaultSettingByType($key);
                }
                $list[$key] = [
                    'name' => $key,
                    'title' =>  __($value),
                    'active' => $key == 'mini' ? 'mini' : '',
                    'list' => $settingList
                ];
            }
        }
        return $list;
    }

    /**
     * 默认配置项
     * @param string $group_type 类型
     * @return array $list;
     */
    private function getDefaultSettingByType(string $group_type = 'mini'): array {

        if($group_type == 'mini') {
            $list[$group_type] = [
                ['name' => 'mini_appid', 'title' => __('mini_appid') ,'value' => '', 'type' => 'string', 'tip' =>'', 'rule' => 'required', 'placeholder' => __('Please input anything')],
                ['name' => 'mini_appsecret', 'title' => __('mini_appsecret') ,'value' => '', 'type' => 'string', 'tip' => '', 'rule' => 'required', 'placeholder' => __('Please input anything')],
                ['name' => 'mini_mch_id', 'title' => __('mini_mch_id') ,'value' => '', 'type' => 'string', 'tip' => '', 'rule' => 'required', 'placeholder' => __('Please input anything')],
                ['name' => 'mini_mch_key', 'title' => __('mini_mch_key') ,'value' => '', 'type' => 'string', 'tip' => '', 'rule' => 'required', 'placeholder' => __('Please input anything')],
                ['name' => 'mini_mch_p12', 'title' => __('mini_mch_p12') ,'value' => '', 'type' => 'file', 'tip' => '', 'rule' => '','placeholder' => __('Please upload file')],
                ['name' => 'mini_mch_pem_key', 'title' => __('mini_mch_pem_key') ,'value' => '', 'type' => 'file', 'tip' => '', 'rule' => '', 'placeholder' => __('Please upload file')],
                ['name' => 'mini_mch_pem_cert', 'title' => __('mini_mch_pem_cert') ,'value' => '', 'type' => 'file', 'tip' => '', 'rule' => '', 'placeholder' => __('Please upload file')],
                ['name' => 'mini_ios_switch', 'title' => __('mini_ios_switch') ,'value' => '', 'type' => 'switch', 'tip' => '', 'rule' => ''],
            ];
        }
        return $list[$group_type];
    }

    /**
     * 是否已保存配置
     * @param int $brand_id 品牌id
     * @param string $group_type 类型
     * @return mixed $rows;
     */
    private function getDefaultSettingByData(int $brand_id,string $group_type = 'mini'): array {
        $rows = $this
            ->where(['brand_id' => $brand_id , 'group' => $group_type])
            ->field(['name','title','value','type','tip','rule'])
            ->select();
        return !empty($rows) ? collection($rows)->toArray() : [];
    }

    /**
     * 生成配置文件
     * @param array $config 配置信息
     * @param int $brand_id 品牌商ID
     */
    public function createConfigFile(array $config,int $brand_id): bool {
        //如果没有配置权限无法进行修改
        if (!\app\admin\library\Auth::instance()->check('xilufitness/brand/index/config')) {
            return false;
        }
        $configFile = CONF_PATH . 'extra' . DS . 'xilubrand.php';
        if(file_exists($configFile)){
            $brandConfig = config('xilubrand');
            $config = array_merge($brandConfig[$brand_id],$config);
            $brandConfig[$brand_id] = $config;
        } else {
            $brandConfig[$brand_id] = $config;
        }
        file_put_contents($configFile,
        '<?php' . "\n\nreturn " . var_export_short($brandConfig) . ";\n");
        return  true;
    }
}