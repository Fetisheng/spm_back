<?php

namespace app\admin\model\xilufitness\user;

use think\Model;


class Account extends Model
{

    // 表名
    protected $name = 'xilufitness_user_account';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    protected static function init()
    {
       self::afterInsert(function ($row){
            $pk = $row->getPk();
            $model = new \app\admin\model\xilufitness\user\Index;
            $user_info = $model->where(['id' => $row['user_id'] ?? 0, 'brand_id' => $row['brand_id'] ?? 0])->find();
            if(!empty($user_info) && !empty($row['amount'])){
                $update_data['before_account'] = $user_info['account'] ?? 0;
                if($row['amount_type'] == 1){
                    $update_data['after_account'] = bcadd($user_info['account'],$row['amount'],0);
                } else {
                    $update_data['after_account'] = bcsub($user_info['account'],$row['amount'],0);
                }
                $update_data['updatetime'] = time();
                $result = $row->getQuery()->where($pk,$row[$pk])->update($update_data);
                if(false !== $result){
                    $user_info->allowField(true)->save(['account' => $update_data['after_account'] ?? 0]);
                }
            }
       });
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
