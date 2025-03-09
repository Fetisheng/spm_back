<?php

namespace app\admin\validate\xilufitness\coach;

use think\Validate;

class Index extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'shop_ids' => 'require',
        'coach_name' => 'require',
        'coach_mobile' => 'require|unique:xilufitness_coach,coach_mobile^brand_id'
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
        'add'  => ['brand_id','shop_ids','coach_name','coach_mobile'],
        'edit' => ['brand_id','shop_ids','coach_name','coach_mobile'],
    ];

    /**
     * 字段描述
     */
    protected $field = [];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id' => __('Brand_id'),
            'shop_ids' => __('Shop_ids'),
            'coach_name' => __('Coach_name'),
            'coach_mobile' => __('Coach_mobile'),
        ];
        $this->message = [
            'coach_mobile.unique' => __('Coach_mobile is already exists')
        ];
        parent::__construct($rules, $message, $field);
    }

}
