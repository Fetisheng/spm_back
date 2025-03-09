<?php


namespace addons\xilufitness\validate;


use think\Validate;

class OrderRecharge extends Validate
{

    /**
     * 验证规则
     */
    protected $rule = [
        'user_id' => 'require',
        'brand_id'    => 'require',
        'order_no'    => 'require',
        'recharge_id'    => 'require',
        'pay_amount'    => 'require',
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
            'user_id' => __('User_id'),
            'brand_id' => __('Brand_id'),
            'order_no' => __('Order_no'),
            'recharge_id' => __('Recharge_id'),
            'pay_amount' => __('Pay_amount')
        ];
        parent::__construct($rules, $message, $field);
    }

}