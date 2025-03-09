<?php

namespace app\admin\validate\xilufitness\shop;

use think\Validate;

class Index extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'brand_id' => 'require',
        'username' => 'require|regex:\w{3,30}|unique:xilufitness_shop,username',
        'password' => 'require|regex:\S{6,32}',
        'shop_name' => 'require|unique:xilufitness_shop,shop_name',
        'shop_mobile' => 'require|unique:xilufitness_shop,shop_mobile',
        'shop_image' => 'require',
        'shop_images' => 'require',
        'province_id' => 'require',
        'city_id' => 'require',
        'area_id' => 'require',
        'address' => 'require',
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
        'add'  => ['brand_id','username','password','shop_name','shop_mobile','shop_image',
            'shop_images','province_id','city_id','area_id','address'],
        'edit' => ['brand_id','username','shop_name','shop_mobile','shop_image',
            'shop_images','province_id','city_id','area_id','address'],
    ];

    /**
     * 字段
     */
    protected $field = [];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'brand_id'  => __('Brand_id'),
            'username'  => __('Username'),
            'password'  => __('Password'),
            'shop_name'  => __('Shop_name'),
            'shop_mobile'  => __('Shop_mobile'),
            'shop_image'  => __('Shop_image'),
            'shop_images'  => __('Shop_images'),
            'province_id'   => __('Province_city_area'),
            'city_id'       => __('Province_city_area'),
            'area_id'       => __('Province_city_area'),
            'address'       => __('Address'),
        ];
        $field = array_merge($this->field,$field);
        $this->message = array_merge($this->message,[
            'username.regex'    => __('Please input correct username'),
            'username.unique'   => __('Username already exists'),
            'password.regex'    => __('Please input correct password'),
            'shop_name.unique'  => __('Shop_name already exists'),
            'shop_mobile.unique'    => __('Shop_mobile already exists')
        ]);
        parent::__construct($rules, $message, $field);
    }

}
