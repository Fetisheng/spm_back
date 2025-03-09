<?php

namespace app\admin\model\xilufitness\lable;

use think\Model;


class Index extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_lable';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'lable_type_text',
        'status_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getLableTypeList()
    {
        return ['course' => __('Course'), 'coach' => __('Coach'), 'camp' => __('Camp')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getLableTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['lable_type']) ? $data['lable_type'] : '');
        $list = $this->getLableTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
