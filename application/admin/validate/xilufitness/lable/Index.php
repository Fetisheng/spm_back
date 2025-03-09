<?php

namespace app\admin\validate\xilufitness\lable;

use think\Validate;

class Index extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'lable_name' => 'require'
    ];

    /**
     * 提示消息
     */
    protected $message = [
    ];

    /**
     * 字段描述
     */
    protected $field = [];

    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['brand_id','lable_name'],
        'edit' => ['brand_id','lable_name'],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id' => __('Brand_id'),
            'lable_name' => __('Lable_name')
        ];
        $this->field = array_merge($this->field,$field);
        parent::__construct($rules, $message, $field);
    }

}
