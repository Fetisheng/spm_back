<?php

namespace app\admin\validate\xilufitness\coach;

use think\Validate;

class Group extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'group_name' => 'require'
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
        'add'  => ['brand_id','group_name'],
        'edit' => ['brand_id','group_name'],
    ];

    /**
     * 字段描述
     */
    protected $field = [];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id' => __('Brand_id'),
            'group_name' => __('Group_name')
        ];
        parent::__construct($rules, $message, $field);
    }

}
