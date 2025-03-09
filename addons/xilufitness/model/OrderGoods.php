<?php


namespace addons\xilufitness\model;


use addons\xilufitness\services\CourseService;
use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Model;

class OrderGoods extends Model
{

    use BaseModel;


    // 表名
    protected $name = 'xilufitness_order_goods';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'course_camp'
    ];

    //获取课程/活动/信息
    public function getCourseCampAttr($value,$data){
        $value = $value ? $value : ($data['goods_id'] ?? 0);
        $info = [];
        if($data['order_type'] != 3){
            $info = CourseService::getInstance()->getDetail($value);
        } elseif ($data['order_type'] == 3){
            $info = CourseService::getInstance()->getCampDetail($value);
        }
        return $info['info'] ?? [];
    }

}