<?php

namespace app\admin\model\xilufitness\user;

use think\Model;


class Media extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_user_media';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    /**
     * 关联用户
     */
    public function user(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\user\\Index','user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联品牌
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联勋章
     */
    public function medal(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\activity\\Medal','media_id','id',[],'LEFT')->setEagerlyType(0);
    }

    







}
