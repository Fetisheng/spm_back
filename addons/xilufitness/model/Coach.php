<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Model;

class Coach extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_coach';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'lable_list',
        'group_name',
        'group_image'
    ];

    /**
     * 获取标签
     */
    public function getLableListAttr($value,$data){
        $list = Db::name('xilufitness_lable')->where(['status' => 'normal', 'id' => ['in',explode(',',$data['lable_ids'] ?? '') ?? [-1]] ])->field(['lable_name'])->select();
        return !empty($list) ? array_column($list,'lable_name') : [];
    }

    /**
     * 获取等级名称
     */
    public function getGroupNameAttr($value,$data){
        $value = $value ? $value : ($data['coach_group_id'] ?? 0);
        return Db::name('xilufitness_coach_group')->where(['id' => $value])->value('group_name');
    }

    /**
     * 获取等级logo
     */
    public function getGroupImageAttr($value,$data){
        $value = $value ? $value : ($data['coach_group_id'] ?? 0);
        $model = new \addons\xilufitness\model\CoachGroup;
        $info =  $model->where(['id' => $value])->field(['group_image'])->find();
        return $info['xilufitness_urls']['group_image'] ?? '';
    }

    /**
     * 全局查询
     */
    public function scopeNormal($query){
        return $query->where('status','normal');
    }


}