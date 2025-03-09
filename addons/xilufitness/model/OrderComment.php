<?php


namespace addons\xilufitness\model;


use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Model;

class OrderComment extends Model
{
    use BaseModel;

    // 表名
    protected $name = 'xilufitness_user_comment';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'createtime_txt'
    ];

    public function getCreatetimeTxtAttr($value,$data){
        $value = $value ? $value : ($data['createtime'] ?? time() );
        return date('Y.m.d',$value);
    }

    protected static function init()
    {
       self::afterInsert(function ($row){
            $count = Db::name('xilufitness_user_comment')->where(['shop_id' => $row['shop_id']])->count('*');
            $sum_star = Db::name('xilufitness_user_comment')->where(['shop_id' => $row['shop_id']])->sum('star');
            $avage_star = $count > 0 ? $sum_star/$count : 0;
            Db::name('xilufitness_shop')->where(['id' => $row['shop_id'] ?? 0])->update(['star' => $avage_star]);
       });
    }

    /**
     * 关联用户
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id','id',[],'LEFT')->setEagerlyType(0);
    }

}