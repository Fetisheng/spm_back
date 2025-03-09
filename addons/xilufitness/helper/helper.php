<?php

if(!function_exists('xilufitness_set_id_value')){
    /**
     * 主健值加密
     * @param $value
     */
    function xilufitness_set_id_value($value){
        if(!is_numeric($value)){
            return $value;
        }
        return \addons\xilufitness\library\Aescbc::encryptWithOpenssl(urlencode($value));
    }
}

if(!function_exists('xilufitness_get_id_value')){
    /**
     * 主健值解密
     * @param $value
     */
    function xilufitness_get_id_value($value){
        if(is_numeric($value)){
            return $value;
        }
        return \addons\xilufitness\library\Aescbc::decryptWithOpenssl(urldecode($value));
    }
}

if(!function_exists('xilufitnessGetCityByLatLng')){
    /**
    /**
     * 根据经纬度
     * 获取城市信息
     */
    function xilufitnessGetCityByLatLng(string $lat, string $lng, $key=''): array {
        $xilufitness_config = get_addon_config('xilufitness');
        if(!empty($xilufitness_config['map_ak'])){
            $key = $xilufitness_config['map_ak'];
        }
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?location=' . ($lat . ',' . $lng) . '&key=' . $key;
        $result = json_decode(\fast\Http::get($url),true);
        $data = [];
        if (isset($result['status']) && $result['status'] == 0) {
            $data['province'] = $result['result']['address_component']['province'] ?? '';
            $data['city'] = $result['result']['address_component']['city'] ?? '';
            $data['area'] =  $result['result']['address_component']['district'] ?? '';
        }
        return $data;
    }
}

if(!function_exists('xilufitness_build_order_no')){
    /**
     * 创建订单号
     */
    function xilufitness_build_order_no(){
        $snowId = \addons\xilufitness\library\SnowFlake::getInstance()->createID();
        return date('YmdHms',time()).substr($snowId,-16,16);
    }
}
