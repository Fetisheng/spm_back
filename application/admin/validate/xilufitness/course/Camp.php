<?php

namespace app\admin\validate\xilufitness\course;

use think\Validate;

class Camp extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id'  => 'require',
        'title'     => 'require|unique:xilufitness_camp,title^brand_id',
        'thumb_image'     => 'require',
        'lable_ids'     => 'require',
        'content'     => 'require',
        'camp_price'  => 'require',
        'write_off_price' => 'require',
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
            'title'    => __('Title'),
            'thumb_image'    => __('Thumb_image'),
            'lable_ids'    => __('Lable_ids'),
            'content'    => __('Content'),
            'camp_price' => __('Camp_price'),
            'write_off_price' => __('Write_off_price')
        ];
        $this->message = [
            'title.unique' => __('Title is already exists')
        ];
        parent::__construct($rules, $message, $field);
    }

}
