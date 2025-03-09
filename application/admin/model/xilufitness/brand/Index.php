<?php

namespace app\admin\model\xilufitness\brand;

use addons\xilufitness\library\SnowFlake;
use think\Model;
use traits\model\SoftDelete;

class Index extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'xilufitness_brand';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];

    /**
     * 模型事件监听
     * 生成品牌商的唯一key值
     */
    protected static function init()
    {
       self::afterInsert(function ($row){
            $pk = $row->getPk();
            $brand_key = SnowFlake::getInstance()->createID();
            $row->getQuery()->where($pk,$row[$pk])->update(['brand_key' => $brand_key]);
       });
    }


    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
