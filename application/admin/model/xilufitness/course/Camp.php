<?php

namespace app\admin\model\xilufitness\course;

use think\Model;


class Camp extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_camp';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'lable_names'
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
     * 标签显示
     */
    public function getLableNamesAttr($value,$data){
        $model = new \app\admin\model\xilufitness\lable\Index;
        $lable_ids = $value ? $value : ($data['lable_ids'] ?? '');
        $lable_list = $model
            ->where('id','in',$lable_ids)
            ->field(['lable_name'])
            ->select();
        return !empty($lable_list) ? implode(',',array_column($lable_list,'lable_name')) : '';
    }

    /**
     * 关联查询
     * 所属品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }




}
