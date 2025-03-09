<?php


namespace addons\xilufitness\traits;


use addons\xilufitness\library\Aescbc;

trait BaseModel
{

    /**
     * 初始化操作
     */
    protected function initialize(){
        $class = get_class($this);
        if (!isset(static::$initialized[$class])) {
            static::$initialized[$class] = true;
            static::init();
        }
        $this->append = array_merge($this->append ?? [],['xilufitness_urls']);

    }

    /**
     * 未所有的图片自动添加链接
     */
    public function getXilufitnessUrlsAttr($value,$data){
        $values = [];
        if(!empty($data)){
            foreach ($data as $key => $val){
                if(false !== strpos($key,'image')){
                    $values[$key] = cdnurl($val,true);
                }
                if(false !== strpos($key,'images')){
                    $image_list = array_filter(explode(',',$val));
                    foreach ($image_list as $kk => $vv){
                        $image_list[$kk] = cdnurl($vv,true);
                    }
                    $values[$key] = $image_list;
                }
                if(false !== strpos($key,'avatar')){
                    $values[$key] = !empty($val) ? cdnurl($val,true) : '';
                }
            }
        }
        return $values;
    }

    /**
     * 主健加密
     */
    public function getIdAttr($value,$data){
        $value = $value ? $value : $data['id'] ?? '';
        return urlencode(Aescbc::encryptWithOpenssl($value));
    }

}