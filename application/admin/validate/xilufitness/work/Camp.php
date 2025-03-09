<?php

namespace app\admin\validate\xilufitness\work;

use think\Validate;

class Camp extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'camp_id' => 'require',
        'shop_id' => 'require',
        'coach_id' => 'require',
        'start_at' => 'require',
        'end_at' => 'require',
        'class_duration' => 'require',
        'camp_count' => 'require',
        'total_count' => 'require',
        'plan_data' => 'require'
    ];
    /**
     * 提示消息
     */
    protected $message = [
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['brand_id','camp_id','shop_id','coach_id','start_at','end_at','class_duration','camp_count','total_count','plan_data'],
        'edit' => ['brand_id','camp_id','shop_id','coach_id','start_at','end_at','class_duration','camp_count','total_count','plan_data'],
    ];

    /**
     * 字段描述
     */
    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id' => __('Brand_id'),
            'camp_id' => __('Camp_id'),
            'shop_id' => __('Shop_id'),
            'coach_id' => __('Coach_id'),
            'start_at' => __('Start_at'),
            'end_at' => __('End_at'),
            'class_duration' => __('Class_duration'),
            'camp_count' => __('Camp_count'),
            'total_count' => __('Total_count'),
            'plan_data' => __('Camp_plan'),
        ];
        parent::__construct($rules, $message, $field);
    }

}
