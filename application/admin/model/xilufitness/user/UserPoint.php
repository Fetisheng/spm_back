<?php

namespace app\admin\model\xilufitness\user;

use think\Model;


class UserPoint extends Model
{

    

    

    // 表名
    protected $name = 'xilufitness_user_point';
    
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
            $userInfo = $model->where(['id' => $row['user_id'], 'brand_id' => $row['brand_id']])->find();
            if(!empty($row['point']) && !empty($userInfo)){
                $update_data['updatetime'] = time();
                $update_data['before_point'] = $userInfo['point'] ?? 0;
                if($row['point_type'] == 1){
                    $update_data['after_point'] = bcadd($userInfo['point'],abs($row['point']));
                } else {
                    $update_data['after_point'] = bcsub($userInfo['point'],abs($row['point']));
                }
                $result = $row->getQuery()->where($pk,$row[$pk])->update($update_data);
                if(false !== $result){
                    $userInfo->allowField(true)->save(['point' => $update_data['after_point'] ?? 0]);
                }
            }
       });
    }


    /**
     * 获取积分规则
     */
    public function getRuleList(){
        $model = new \app\admin\model\xilufitness\user\PointRule;
        $list = $model
            ->field(['title','rule_type'])
            ->select();
        $types = array_column($list,'title','rule_type');
        return $types ?? [];
    }

    /**
     * 关联用户
     */
    public function user(){
        return $this->belongsTo(Index::class,'user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联品牌商
     */
    public function brand(){
        return $this->belongsTo('\\app\\admin\\model\\xilufitness\\brand\\Index','brand_id','id',[],'LEFT')->setEagerlyType(0);
    }







}
