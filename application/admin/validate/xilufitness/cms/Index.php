<?php

namespace app\admin\validate\xilufitness\cms;

use think\Validate;

class Index extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id'      => 'require',
        'title'         => 'require',
        'cms_type'      => 'require',
        'content'       => 'require'
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
        'add'  => ['brand_id', 'title', 'cms_type', 'content'],
        'edit' => ['brand_id', 'title', 'cms_type', 'content'],
    ];

    /**
     * 字段描述
     */
    protected $field = [];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id'      => __('Brand_id'),
            'title'         => __('Title'),
            'cms_type'      => __('Cms_type'),
            'content'       => __('Content')
        ];
        parent::__construct($rules, $message, $field);
    }

}
