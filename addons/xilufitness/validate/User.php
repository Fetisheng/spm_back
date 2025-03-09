<?php


namespace addons\xilufitness\validate;


use think\Validate;

class User extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'nickname' => 'require'
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

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'nickname' => __('Nickname')
        ];
        parent::__construct($rules, $message, $field);
    }

}