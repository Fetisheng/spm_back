<?php

namespace app\admin\validate\xilufitness\course;

use think\Validate;

class Index extends Validate
{

    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'course_cate_pid' => 'require',
        'course_cate_id' => 'require',
        'title' => 'require|unique:xilufitness_course,brand_id^title',
        'lable_ids' => 'require',
        'thumb_image' => 'require',
        'thumb_images' => 'require',
        'content' => 'require',
        'course_price' => 'require',
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
        'add'  => ['brand_id','course_cate_pid','course_cate_id','title','lable_ids','thumb_image','thumb_images','content','course_price','write_off_price'],
        'edit' => ['brand_id','course_cate_pid','course_cate_id','title','lable_ids','thumb_image','thumb_images','content','course_price','write_off_price'],
    ];

    /**
     * 字段描述
     */
    protected $field = [];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id'  => __('Brand_id'),
            'course_cate_pid'  => __('Course_cate_pid'),
            'course_cate_id'  => __('Course_cate_id'),
            'title'  => __('Title'),
            'lable_ids'  => __('Lable_ids'),
            'thumb_image' => __('Thumb_image'),
            'thumb_images' => __('Thumb_images'),
            'content'  => __('Content'),
            'course_price' => __('Course_price'),
            'write_off_price' => __('Write_off_price')
        ];
        $this->message = [
            'title.unique' => __('Title is already exists')
        ];
        parent::__construct($rules, $message, $field);
    }

}
