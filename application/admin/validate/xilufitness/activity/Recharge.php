<?php

namespace app\admin\validate\xilufitness\activity;

use think\Validate;

class Recharge extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'recharge_amount' => 'require',
        'account_amount' => 'require'
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
            'recharge_amount' => __('Recharge_amount'),
            'account_amount' => __('Account_amount'),
        ];
        parent::__construct($rules, $message, $field);
    }

}
