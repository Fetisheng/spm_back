<?php

namespace app\admin\validate\xilufitness\work;

use think\Validate;

class Course extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'course_id' => 'require',
        'shop_id' => 'require',
        'coach_id' => 'require',
        'class_time' => 'require',
        'start_at' => 'require',
        'end_at' => 'require',
        'sign_count' => 'require',
        'wait_count' => 'require',
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
        'add'  => ['brand_id','course_id','shop_id','coach_id','class_time','start_at','end_at','sign_count','wait_count'],
        'edit' => ['brand_id','course_id','shop_id','coach_id','class_time','start_at','end_at','sign_count','wait_count'],
    ];

    /**
     * 字段描述
     */
    protected $field = [];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id' => __('Brand_id'),
            'course_id' => __('Course_id'),
            'shop_id' => __('Shop_id'),
            'coach_id' => __('Coach_id'),
            'class_time' => __('Class_time'),
            'start_at' => __('Start_at'),
            'end_at' => __('End_at'),
            'sign_count' => __('Sign_count'),
            'wait_count' => __('Wait_count'),
        ];
        parent::__construct($rules, $message, $field);
    }

}
