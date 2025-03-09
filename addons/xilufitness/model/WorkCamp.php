<?php


namespace addons\xilufitness\model;


use addons\xilufitness\library\Aescbc;
use addons\xilufitness\traits\BaseModel;
use app\common\library\Auth;
use think\Model;

class WorkCamp extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_work_camp';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'start_at',
        'end_at',
        'plans'
    ];

    public function getStartAtAttr($value,$data){
        $value = $value ? $value : ($data['start_at'] ?? '');
        return !empty($value) && is_numeric($value) ? date('m月d日',$value) : $value;
    }

    public function getEndAtAttr($value,$data){
        $value = $value ? $value : ($data['end_at'] ?? '');
        return !empty($value) && is_numeric($value) ? date('m月d日',$value) : $value;
    }

    /**
     * 关联查询活动
     */
    public function camp(){
        return $this->belongsTo('Camp','camp_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联门店
     */
    public function shop(){
        return $this->belongsTo(Shop::class,'shop_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联教练
     */
    public function coach(){
        return $this->belongsTo(Coach::class,'coach_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 计划时间
     */
    public function getplansAttr($value,$data){
        $model = new WorkCampPlan();
        return $model->where(['work_camp_id' => $data['id'] ?? 0])
            ->field(['id','day_date','day_start_at','day_end_at','week'])
            ->select();
    }

}