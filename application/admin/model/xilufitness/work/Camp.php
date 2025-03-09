<?php

namespace app\admin\model\xilufitness\work;

use think\Model;


class Camp extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_work_camp';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];

    //时间监听
    public static function init()
    {
        //新增
        self::afterInsert(function ($row){
           $pk = $row->getPk();
           if(!empty($row['plan_data'])){
               $planModel = new \app\admin\model\xilufitness\work\CampPlan;
               $campModel = new \app\admin\model\xilufitness\course\Camp;
               $plan_data = $row['plan_data'];
               foreach ($plan_data as $key => $val){
                   $plan_data[$key]['work_camp_id'] = $row[$pk];
                   $plan_data[$key]['week'] = date('w',strtotime($val['day_date']));
               }
               $planModel->allowField(true)->saveAll($plan_data);
               $campInfo = $campModel
                   ->where(['id' => $row['camp_id'] ])
                   ->field(['camp_price','write_off_price','market_price'])
                   ->find();
               $row->getQuery()->where($pk,$row[$pk])->update([
                   'class_count' => count($plan_data),
                   'camp_price'  => $campInfo['camp_price'],
                   'write_off_price' => $campInfo['write_off_price'],
                   'market_price' => $campInfo['market_price']
               ]);
           }
        });
       //更新
        self::afterUpdate(function ($row){
            $pk = $row->getPk();
            if(!empty($row['plan_data'])){
                $planModel = new \app\admin\model\xilufitness\work\CampPlan;
                $campModel = new \app\admin\model\xilufitness\course\Camp;
                $campInfo = $campModel
                    ->where(['id' => $row['camp_id'] ])
                    ->field(['camp_price','write_off_price','market_price'])
                    ->find();
                $plan_data = $row['plan_data'];
                foreach ($plan_data as $key => $val){
                    $plan_data[$key]['work_camp_id'] = $row[$pk];
                    $plan_data[$key]['week'] = date('w',strtotime($val['day_date']));
                }
                $plan_ids = array_filter(array_column($plan_data,'id'));
                if(!empty($plan_ids)){
                    //删除已删除数据
                    $planModel->where(['work_camp_id' => $row[$pk], 'id' => ['notin',$plan_ids]])->delete();
                }

                $planModel->allowField(true)->saveAll($plan_data);
                $row->getQuery()->where($pk,$row[$pk])->update([
                    'class_count' => count($plan_data),
                    'camp_price'  => $campInfo['camp_price'],
                    'write_off_price' => $campInfo['write_off_price'],
                    'market_price' => $campInfo['market_price']
                ]);
            }
        });
    }


    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'complete' => __('Complete') ,'failed' => __('Failed'),'hidden' => __('Hidden')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    //开始时间
    protected function setStartAtAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    //结束时间
    protected function setEndAtAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    /**
     * 关联品牌商
     * 查询
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 活动
     * 查询
     */
    public function camps(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\course\\Camp','camp_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联门店
     * 查询
     */
    public function shop(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\shop\\Index','shop_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联教练
     * 查询
     */
    public function coach(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\coach\\Index','coach_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 获取计划明细
     * @param int $work_camp_id 营期id
     */
    public function getPlanList(int $work_camp_id): array {
        $model = new \app\admin\model\xilufitness\work\CampPlan;
        $list = $model
            ->where(['work_camp_id' => $work_camp_id])
            ->field(['id','day_date','day_start_at','day_end_at'])
            ->select();
        return !empty($list) ? collection($list)->toArray() : [];
    }



}
