<?php

namespace app\admin\validate\xilufitness\activity;

use think\Validate;

class Medal extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'medal_name' => 'require',
        'thumb_image' => 'require',
        'class_time' => 'require',
        'description' => 'require',
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
        'add'  => ['brand_id','medal_name','thumb_image','class_time','description'],
        'edit' => ['brand_id','medal_name','thumb_image','class_time','description'],
    ];

    /**
     * 字段描述
     */
    protected $field = [];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id' => __('Brand_id'),
            'medal_name' => __('Medal_name'),
            'thumb_image' => __('Thumb_image'),
            'class_time' => __('Class_time'),
            'description' => __('Description'),
        ];
        parent::__construct($rules, $message, $field);
    }

}
