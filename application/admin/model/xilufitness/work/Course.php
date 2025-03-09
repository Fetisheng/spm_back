<?php

namespace app\admin\model\xilufitness\work;

use think\Model;


class Course extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_work_course';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'class_time_text',
        'status_text'
    ];

    /**
     * 监听事件新增/更新
     */
    public static function init()
    {
        self::afterInsert(function ($row){
            $pk = $row->getPk();
            $courseModel = new \app\admin\model\xilufitness\course\Index;
            $courseInfo = $courseModel
                ->where(['id' => $row['course_id'] ])
                ->field(['course_price','write_off_price','course_type','market_price'])
                ->find();
            $row->getQuery()->where($pk,$row[$pk])->update([
                'course_price' => $courseInfo['course_price'],
                'write_off_price' => $courseInfo['write_off_price'],
                'course_type' => $courseInfo['course_type'],
                'market_price' => $courseInfo['market_price']
            ]);
        });
        self::afterUpdate(function ($row){
            $pk = $row->getPk();
            $courseModel = new \app\admin\model\xilufitness\course\Index;
            $courseInfo = $courseModel
                ->where(['id' => $row['course_id'] ])
                ->field(['course_price','write_off_price','course_type','market_price'])
                ->find();
            $row->getQuery()->where($pk,$row[$pk])->update([
                'course_price' => $courseInfo['course_price'],
                'write_off_price' => $courseInfo['write_off_price'],
                'course_type' => $courseInfo['course_type'],
                'market_price' => $courseInfo['market_price']
            ]);
        });
    }


    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'complete' => __('Complete'),'failed' => __('Failed') ,'hidden' => __('Hidden')];
    }

    public function getStartAtAttr($value,$data){
        $value = $value ? $value : (isset($data['start_at']) ? $data['start_at'] : '');
        return is_numeric($value) ? date("H:i:s", $value) : $value;
    }

    public function getEndAtAttr($value,$data){
        $value = $value ? $value : (isset($data['start_at']) ? $data['start_at'] : '');
        return is_numeric($value) ? date("H:i:s", $value) : $value;
    }


    public function getClassTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['class_time']) ? $data['class_time'] : '');
        return is_numeric($value) ? date("Y-m-d", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setClassTimeAttr($value)
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
     * 关联课程
     * 查询
     */
    public function courses(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\course\\Index','course_id','id',[],'LEFT')->setEagerlyType(0);
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


}
