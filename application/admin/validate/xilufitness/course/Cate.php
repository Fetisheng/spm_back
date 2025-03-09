<?php

namespace app\admin\validate\xilufitness\course;

use think\Validate;

class Cate extends Validate
{

    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'cate_name' => 'require|unique:xilufitness_course_cate,cate_name^brand_id'
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
        'add'  => [],
        'edit' => [],
    ];

    /**
     * 字段描述
     */
    protected $field = [];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id' => __('Brand_id'),
            'cate_name' => __('Cate_name')
        ];
        $this->message = [
            'cate_name.unique' => __('Cate_name is already exists')
        ];
        parent::__construct($rules, $message, $field);
    }

}
