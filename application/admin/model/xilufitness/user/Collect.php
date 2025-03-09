<?php

namespace app\admin\model\xilufitness\user;

use think\Db;
use think\Model;


class Collect extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_user_collect';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_name'
    ];

    /**
     * 获取收藏类型
     */
    public function getTypes(){
        return [1 => __('Shop'), 2 => __('Coach')];
    }

    /**
     * 获取收藏类型的名称
     */
    public function getTypeNameAttr($value,$data){
          if($data['is_type'] == 1){
              $name = Db::name('xilufitness_shop')
                  ->where(['brand_id' => $data['brand_id'] ?? 0, 'id' => $data['data_id'] ?? 0])
                  ->value('shop_name');
          } else {
              $name = Db::name('xilufitness_coach')
                  ->where(['brand_id' => $data['brand_id'] ?? 0, 'id' => $data['data_id'] ?? 0])
                  ->value('coach_name');
          }
          return $name ?? '';
    }

    /**
     * 关联用户
     */
    public function user(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\user\\Index','user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联品牌
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }
    







}
