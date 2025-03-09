<?php

namespace app\admin\validate\xilufitness\activity;

use think\Validate;

class Coupon extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'title'    => 'require',
        'meet_amount' => 'require',
        'discount_amount' => 'require'
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
        'add'  => ['brand_id','title','meet_amount','discount_amount'],
        'edit' => ['brand_id','title','meet_amount','discount_amount'],
    ];

    /**
     * 字段描述
     */
    protected $field = [];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id' => __('Brand_id'),
            'title' => __('Title'),
            'meet_amount' => __('Meet_amount'),
            'discount_amount' => __('Discount_amount'),
        ];
        parent::__construct($rules, $message, $field);
    }

}
