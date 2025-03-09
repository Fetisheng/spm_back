<?php


namespace addons\xilufitness\validate;


use think\Validate;

class UserAccount extends Validate
{

    /**
     * 验证规则
     */
    protected $rule = [
        'mobile' => 'require',
        'openid'    => 'require',
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
            'mobile' => __('Mobile'),
            'openid' => __('Openid'),
        ];
        parent::__construct($rules, $message, $field);
    }

}