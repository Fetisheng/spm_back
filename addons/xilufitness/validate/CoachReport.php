<?php


namespace addons\xilufitness\validate;


use think\Validate;

class CoachReport extends Validate
{

    /**
     * 验证规则
     */
    protected $rule = [
        'coach_id' => 'require',
        'start_at' => 'require',
        'end_at'    => 'require',
        'description'    => 'require',
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
            'coach_id'       => __('Coach_id'),
            'start_at' => __('Start_at'),
            'end_at' => __('End_at'),
            'description' => __('Report_description'),
        ];
        parent::__construct($rules, $message, $field);
    }

}