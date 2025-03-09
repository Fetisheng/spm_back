<?php

namespace app\admin\model\xilufitness\cms;

use think\Model;


class Index extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_cms';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
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

    /**
     * 文章类型
     */
    public function getTypeList(){
        return [1 => __('Type_1'),9 => __('Type_9'),2 => __('Type_2'), 3 => __('Type_3'), 4 => __('Type_4'), 5 => __('Type_5'), 6 => __('Type_6'), 7 => __('Type_7'), 8 => __('Type_8')];
    }

    /**
     * 关联查询
     * 品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }




}
