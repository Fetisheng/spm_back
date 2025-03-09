<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Model;

class Camp extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_camp';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'lable_list'
    ];

    /**
     * 获取标签
     */
    public function getLableListAttr($value,$data){
        $list = Db::name('xilufitness_lable')->where(['status' => 'normal', 'id' => ['in',explode(',',$data['lable_ids'] ?? '') ?? [-1]] ])->field(['lable_name'])->select();
        return !empty($list) ? array_column($list,'lable_name') : [];
    }

}