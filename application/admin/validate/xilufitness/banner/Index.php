<?php

namespace app\admin\validate\xilufitness\banner;

use think\Validate;

class Index extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id'          => 'require',
        'title'             => 'require',
        'thumb_image'       => 'require',
        'banner_position'   => 'require'
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
        'add'  => ['brand_id','title','thumb_image','banner_position'],
        'edit' => [],
    ];

    /**
     * 字段描述
     */
    protected $field = [];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id'          => __('Brand_id'),
            'title'             => __('Title'),
            'thumb_image'       => __('Thumb_image'),
            'banner_position'   => __('Banner_position')
        ];
        parent::__construct($rules, $message, $field);
    }

}
