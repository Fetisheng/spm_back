<?php

namespace app\admin\model\xilufitness\user;

use think\Model;


class PointRule extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_point_rule';
    
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
     * 关联查询
     * 品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 获取规则类型
     */
    public function getTypeList(){
        return [ 1 => __('Pay for free points'), 2 => __('Sign in courses'), 3 => __('Invite friends'), 4 => __('Unlock Medal')];
    }




}
