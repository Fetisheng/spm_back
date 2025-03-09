<?php

namespace app\admin\model\xilufitness\course;

use think\Db;
use think\Model;


class Cate extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_course_cate';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'brand_name'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
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

    /**
     * 获取品牌商名称
     */
    public function getBrandNameAttr($value,$data){
        $model = new \app\admin\model\xilufitness\brand\Index;
        $brand_id = $value ? $value : ($data['brand_id'] ?? 0);
        return $model->where('id',$brand_id)->value('brand_name');
    }




}
