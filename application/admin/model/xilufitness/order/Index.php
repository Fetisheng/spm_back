<?php

namespace app\admin\model\xilufitness\order;

use think\Model;
use traits\model\SoftDelete;

class Index extends Model
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
        'pay_time_text',
        'createtime_text'
    ];

    public function getCreatetimeTextAttr($value, $data){
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

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
     * 关联套餐
     */
    public function recharge(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\activity\\Recharge','data_id','id')->setEagerlyType(0);
    }

    /**
     * 关联会员卡类型
     */
    public function category(){
        return $this->belongsTo('\app\admin\model\xilufitness\card\Category','data_id','id')->setEagerlyType(0);
    }

    /**
     * 关联会员卡类型
     */
    public function package(){
        return $this->belongsTo('\app\admin\model\xilufitness\card\CardPackage','data_id','id')->setEagerlyType(0);
    }
}
