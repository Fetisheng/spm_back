<?php

namespace app\admin\model\xilufitness\order;

use think\Model;
use traits\model\SoftDelete;

class Personal extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'xilufitness_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'pay_time_text'
    ];
    

    



    public function getPayTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_time']) ? $data['pay_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
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

    /**
     * 关联课程
     */
    public function courses(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\course\\Index','course_camp_id','id',[],'LEFT')->setEagerlyType(0);
    }

}
