<?php

namespace app\admin\model\xilufitness\user;

use think\Db;
use think\Model;


class Comment extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_user_comment';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'course_title'
    ];
    

    public function getCourseTitleAttr($value,$data){
        if($data['course_type'] != 3){
            $name = Db::name('xilufitness_course')->where(['id' => $data['course_camp_id'] ?? 0])->value('title');
        } else {
            $name = Db::name('xilufitness_camp')->where(['id' => $data['course_camp_id'] ?? 0])->value('title');
        }
        return $name;
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
     * 关联用户
     */
    public function user(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\user\\Index','user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联门店
     */
    public function shop(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\shop\\Index','shop_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联教练
     */
    public function coach(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\coach\\Index','coach_id','id',[],'LEFT')->setEagerlyType(0);
    }
}
