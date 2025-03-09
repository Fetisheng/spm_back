<?php

namespace app\admin\model\xilufitness\coach;

use think\Db;
use think\Model;


class Index extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_coach';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'coach_sex_text',
        'status_text',
        'shop_names',
        'lable_names'
    ];
    

    
    public function getCoachSexList()
    {
        return ['male' => __('Male'), 'female' => __('Female'), 'unknow' => __('Unknow')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getCoachSexTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['coach_sex']) ? $data['coach_sex'] : '');
        $list = $this->getCoachSexList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    //门店名称显示
    public function getShopNamesAttr($value,$data){
        $model = new \app\admin\model\xilufitness\shop\Index();
        $shop_ids = $value ? $value : ($data['shop_ids'] ?? '');
        $shop_list = $model
            ->where('id','in',$shop_ids)
            ->field(['shop_name'])
            ->select();
        return !empty($shop_list) ? implode(',',array_column($shop_list,'shop_name')) : '';
    }

    //显示标签名称
    public function getLableNamesAttr($value,$data){
        $model = new \app\admin\model\xilufitness\lable\Index();
        $lable_ids = $value ? $value : ($data['lable_ids'] ?? '');
        $lable_list = $model
            ->where('id','in',$lable_ids)
            ->field(['lable_name'])
            ->select();
        return !empty($lable_list) ? implode(',',array_column($lable_list,'lable_name')) : '';
    }

    /**
     * 关联查询
     * 品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联查询
     * 教练等级
     */
    public function group(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\coach\\Group','coach_group_id','id',[],'LEFT')->setEagerlyType(0);
    }




}
