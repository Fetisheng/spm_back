<?php


namespace app\admin\model\xilufitness\brand;


use think\Model;

class AdminAccess extends Model
{
    // 表名
    protected $name = 'xilufitness_admin_access';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
    ];

    /**
     * 关联后台账号
     */

    public function admin(){
        return $this->belongsTo('\\app\\admin\\model\\Admin','admin_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }

}