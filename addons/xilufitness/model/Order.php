<?php


namespace addons\xilufitness\model;


use addons\xilufitness\services\CourseService;
use addons\xilufitness\services\user\PointService;
use addons\xilufitness\services\user\UserService;
use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Log;
use think\Model;
use traits\model\SoftDelete;

class Order extends Model
{

    use SoftDelete;
    use BaseModel;


    // 表名
    protected $name = 'xilufitness_order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'pay_body',
        'pay_time',
        'goods_num',
        'code_url',
        'code_total_num',
        'show_cancel'
    ];

    public function getOrderStatusAttr($value,$data){
        $value = $value ? $value : $data['order_status'] ?? 0;
        $end_at = $data['endtime'] ?? 0;
        return time() > $end_at && in_array($value,[1,2]) ? 5 : $value;
    }

    // 获取支付title信息
    public function getPayBodyAttr($value,$data){
        $orderGoods = new \addons\xilufitness\model\OrderGoods;
        $goodsName = $orderGoods->where(['order_id' => xilufitness_get_id_value($data['id']) ?? 0])->value('goods_name');
        return $goodsName;
    }

    public function getPayTimeAttr($value,$data){
        $value = $value ? $value : ($data['pay_time'] ?? '');
        return !empty($value) && is_numeric($value) ? date('m.d H:i') : $value;
    }

    // 数量
    public function getGoodsNumAttr($value,$data){
        $value = $value ? $value : (xilufitness_get_id_value($data['id'] ?? 0));
        return $this->getNum($value);
    }

    public function getNum($id){
        $orderGoodsModel = new \addons\xilufitness\model\OrderGoods;
        $nums = $orderGoodsModel->where(['order_id' => xilufitness_get_id_value($id ?? 0)])->sum('num');
        return $nums;
    }

    // 核销码数量
    public function getCodeTotalNumAttr($value,$data){
        $goods_num = $this->getNum($data['id']) ?? 1;
        $order_type = $data['order_type'] ?? 1;
        if($order_type == 0){
            return 0;
        } elseif($order_type == 3){
            $campDetail = CourseService::getInstance()->getCampDetail($data['data_id'] ?? 0);
            $class_count = $campDetail['info']['class_count'] ?? 1;
            return  $goods_num * $class_count;
        } else {
            $campDetail = CourseService::getInstance()->getDetail($data['data_id'] ?? 0);
            $class_count = $campDetail['info']['class_count'] ?? 1;
            return  $goods_num * $class_count;
        }
    }

    //是否显示取消按钮
    public function getShowCancelAttr($value,$data){
        $order_type = $data['order_type'] ?? 1;
        $pay_status = $data['pay_status'] ?? 0;
        if($order_type == 0 || $pay_status == 0){
            return 0;
        } elseif($order_type == 3){
            $start_at = Db::name('xilufitness_work_camp')->where(['id' => $data['data_id']])->value('start_at');
            return $start_at > time() ? 1 : 0;
        } else {
            $start_at = Db::name('xilufitness_work_course')->where(['id' => $data['data_id']])->value('start_at');
            return $start_at > time() ? 1 : 0;
        }
    }

    //获取核销码
    public function getCodeUrlAttr($value,$data){
        $value = $value ? $value : ($data['id'] ?? 0);
        $code_url = url('/addons/xilufitness/order/getCodeInfo',['id' => $value],true,true);
        return $code_url;
    }


    //关联订单明细
    public function goods(){
        return $this->belongsTo(OrderGoods::class,'id','order_id',[],'LEFT')->setEagerlyType(0);
    }

    //关联门店
    public function shop(){
        return $this->belongsTo(Shop::class,'shop_id','id',[],'LEFT')->setEagerlyType(0);
    }

    //关联用户
    public function user(){
        return $this->belongsTo(User::class,'user_id','id',[],'LEFT')->setEagerlyType(0);
    }

    /**
     * 添加订单明细
     * @param int $order_id 订单id
     * @param int $order_type 订单类型
     * @param int $num 数量
     * @param array|object $goodsInfo
     */
    public function addGoods($order_id,$order_type,$num,$goodsInfo){
        $model = new \addons\xilufitness\model\OrderGoods;
        $item['order_id'] = xilufitness_get_id_value($order_id);
        $item['order_type'] = $order_type;
        $item['goods_id'] = xilufitness_get_id_value($goodsInfo['id'] ?? 0);
        if($order_type == 0){
            $item['goods_name'] = '会员充值';
            $item['sale_price'] = $goodsInfo['recharge_amount'] ?? 0;
            $item['total_price'] = round(bcmul($goodsInfo['account_amount'],$num,2),2);
        } elseif($order_type == 1 || $order_type == 2) {
            $item['goods_name'] = $goodsInfo['course']['title'] ?? '';
            $item['thumb_image'] = $goodsInfo['course']['xilufitness_urls']['thumb_image'] ?? '';
            $item['sale_price'] = $goodsInfo['course_price'] ?? 0;
            $item['total_price'] = round(bcmul($goodsInfo['course_price'],$num,2),2);
            $item['course_camp_id'] = $goodsInfo['course_id'] ?? 0;
        }elseif($order_type == 3){
            $item['goods_name'] = $goodsInfo['camp']['title'] ?? '';
            $item['thumb_image'] = $goodsInfo['camp']['xilufitness_urls']['thumb_image'] ?? '';
            $item['sale_price'] = $goodsInfo['camp_price'] ?? 0;
            $item['total_price'] = round(bcmul($goodsInfo['camp_price'],$num,2),2);
            $item['course_camp_id'] = $goodsInfo['camp_id'] ?? 0;
        } elseif($order_type == 4){
            $item['goods_name'] = $goodsInfo['cardname'] ?? '';
            $item['thumb_image'] = $goodsInfo['card_avatar'] ?? '';
            $item['sale_price'] = $goodsInfo['cardprice'] ?? 0;
            $item['total_price'] = round(bcmul($goodsInfo['cardprice'],$num,2),2);
        } elseif($order_type == 5){
            $item['goods_name'] = $goodsInfo['package_name'] ?? '';
            $item['thumb_image'] = $goodsInfo['package_avatar'] ?? '';
            $item['sale_price'] = $goodsInfo['total_price'] ?? 0;
            $item['total_price'] = round(bcmul($goodsInfo['total_price'],$num,2),2);
        }
        $item['num'] = $num;
        return $model->allowField(true)->save($item);
    }

    /**
     * 支付成功
     * @param object $orderInfo
     * @param string $trade_no
     * @param int $pay_type 支付方式
     */
    public function payAfter(object $orderInfo, string $trade_no,int $pay_type=2){
        try {
            Db::startTrans();
            $pay_time = time();
            $result = $orderInfo->save(['pay_status' => 1, 'order_status' => $orderInfo['order_status'] == 0 ? 1 : $orderInfo['order_status'],'pay_time' => $pay_time, 'trade_no' => $trade_no, 'pay_type' => $pay_type]);
            if(false !== $result){
                //修改支付流水数据
                (new \addons\xilufitness\model\CashFlow)->payAfter($orderInfo,0,$pay_time,$pay_type);
                //余额记录变化
                if($orderInfo['order_type'] == 0){
                    $this->userAccountChange('会员充值',$orderInfo['total_amount'],1,$orderInfo['order_type'],$orderInfo,$orderInfo['brand_id'] ?? 0);
                } elseif ($orderInfo['pay_type'] == 1 && $orderInfo['pay_amount'] > 0){
                    $this->userAccountChange('余额支付',$orderInfo['pay_amount'],2,$orderInfo['order_type'],$orderInfo);
                }
                //优惠券状态修改
                if(!empty($orderInfo['coupon_id']) && $orderInfo['coupon_amount']){
                    (new \addons\xilufitness\model\UserCoupon)->save(['coupon_status' => 2],['id' => $orderInfo['coupon_id']]);
                }
                //积分增加
                $this->userPointChange('支付送积分',1,1,$orderInfo,$orderInfo['brand_id']);

            }
            Db::commit();
        } catch (\Exception $e){
            Db::rollback();
            Log::record('支付回调出错:'.$e->getMessage());
        }

    }

    /**
     * 会员充值 余额变化
     * @param float $accountAmount 余额变化
     * @param int $amount_type 余额类型
     * @param int $account_type 账户类型
     * @param object $orderInfo 订单详情
     */
    public function userAccountChange($title,$accountAmount,$amount_type,$account_type,$orderInfo,$brand_id=0){
        return UserService::getInstance()->userAccountChange($title,$orderInfo->user_id,$orderInfo->id,$accountAmount,$amount_type,$account_type,$brand_id);
    }

    /**
     * 会员积分变化
     */
    public function userPointChange($title,$rule_type,$point_type,$orderInfo,$brand_id=0){
        return PointService::getInstance()->sendPoint($title,$orderInfo->user_id,$rule_type,$orderInfo->id,$point_type,$orderInfo->pay_amount,$brand_id);
    }

}