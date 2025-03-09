<?php


namespace addons\xilufitness\services;


use think\Db;

class CouponService extends BaseService
{

    /**
     * 获取优惠券列表
     */
    public function getList(){
        $model = new \addons\xilufitness\model\Coupon;
        $userCouponModel = new \addons\xilufitness\model\UserCoupon;
        $userCouponList = $userCouponModel
            ->where(['user_id' => $this->getUserId(), 'brand_id' => $this->brand_id])
            ->field(['coupon_id'])
            ->select();
        $coupon_ids = array_column($userCouponList,'coupon_id');
        $list = $model
            ->normal()
            ->where(['is_activity' => 0, 'brand_id' => $this->brand_id, 'id' => ['notin',$coupon_ids ?? [-1] ]])
            ->field(['id','title','meet_amount','discount_amount','expire_day','coupon_count'])
            ->order('id desc')
            ->select();
        return ['list' => $list ];
    }

    /**
     * 我的优惠券
     * @param int $status
     */
    public function getMyCoupon(int $status = 1){
        $model = new \addons\xilufitness\model\UserCoupon;
        $list = $model
            ->where(['user_id' => $this->getUserId(), 'brand_id' => $this->brand_id, 'coupon_status' => $status])
            ->field(['id','coupon_id','user_id','title','meet_amount','discount_amount','expire_time','coupon_status'])
            ->select();
        return ['list' => $list];
    }

    /**
     * 获取优惠券详情
     * @param int $id 优惠券id
     * @param int $is_activity 1 参与邀请活动 0 否
     */
    public function getDetail(int $id, int $is_activity = 0){
        $model = new \addons\xilufitness\model\Coupon;
        $info = $model
                ->normal()
                ->where(['is_activity' => $is_activity, 'brand_id' => $this->brand_id, 'id' => $id])
                ->field(['id','title','meet_amount','discount_amount','expire_day','coupon_count','is_activity','invite_num','receive_count'])
                ->find();
        return $info;
    }

    /**
     * 获取下单优惠券详情
     * @param int $user_coupon_id
     */
    public function getUserCouponDetail(int $user_coupon_id){
        $model = new \addons\xilufitness\model\UserCoupon;
        $info = $model
            ->where(['id' => $user_coupon_id, 'coupon_status' => 1, 'expire_time' => ['egt',time()]])
            ->field(['meet_amount','discount_amount','coupon_status','expire_time'])
            ->find();
        return $info;
    }

    /**
     * 下单优惠券
     * @param float $total_price 下单价格
     * @return array
     */
    public function getOrderCoupon(float $total_price=0.00){
        $model = new \addons\xilufitness\model\UserCoupon;
        $list = $model
            ->where(['brand_id' => $this->brand_id, 'user_id' => $this->getUserId(), 'coupon_status' => 1, 'meet_amount' => ['elt',$total_price]])
            ->field(['id','coupon_id','user_id','title','meet_amount','discount_amount','expire_time','coupon_status'])
            ->order('meet_amount asc')
            ->select();
        return ['list' => $list];
    }

    /**
     * 领取优惠券
     * @param int $id 优惠券id
     * @param int $is_activity 是否参与邀请活动 1 是 0 否
     * @return array
     */
    public function getCoupon(int $id,int $is_activity = 0){
        $info = $this->getDetail($id,$is_activity);
        $model = new \addons\xilufitness\model\UserCoupon;
        if(empty($info)){
            $this->resultError('优惠券不存在');
        }
        if($info['coupon_count'] > 0 && $info['receive_count'] >= $info['coupon_count']){
            $this->resultError('优惠券已领取完了');
        }
        try {
            Db::startTrans();
            $data['brand_id'] = $this->brand_id;
            $data['user_id'] = $this->getUserId();
            $data['coupon_id'] = $id;
            $data['title'] = $info['title'];
            $data['meet_amount'] = $info['meet_amount'];
            $data['discount_amount'] = $info['discount_amount'];
            $data['invite_num'] = $is_activity == 1 ? ($info['invite_num'] || 0) : 0;
            if($info['expire_day'] == 0){
                $data['expire_time'] = strtotime("+1 days",time());
            } else {
                $data['expire_time'] = strtotime("+{$info['expire_day']} days",time());
            }
            $result = $model->allowField(true)->save($data);
            Db::commit();
        } catch (\Exception $e){
            Db::rollback();
            $this->resultError($e->getMessage());
        }
        return ['code' => false !== $result ? 1 : 0, 'msg' => false !== $result ? '领取成功' : '领取失败'];
    }

    /**
     * 邀请有礼
     * 赠送优惠券
     * @return array
     */
    public function getInviteList(){
        $model = new \addons\xilufitness\model\Coupon;
        $userCouponModel = new \addons\xilufitness\model\UserCoupon;
        $use_invite_num = $userCouponModel
            ->where(['brand_id' => $this->brand_id, 'user_id' => $this->getUserId()])
            ->sum('invite_num');
        $rec_count = $this->getShareTotalCount($this->getUserId()); //推荐人数
        $list = $model
            ->where(['status' => 'normal', 'is_activity' => 1])
            ->field(['id','title','meet_amount','discount_amount','expire_day','invite_num'])
            ->order('discount_amount asc')
            ->select();
        $share_count = 0;
        foreach ($list as $key => $val){
            $val->append(['is_receive','share_count']);
            $is_receive = $userCouponModel
                ->where(['brand_id' => $this->brand_id, 'user_id' => $this->getUserId(), 'coupon_id' => xilufitness_get_id_value($val['id']) ])
                ->field(['id'])
                ->find();
            $val->is_receive = !empty($is_receive) ? 1 : 0;
            $left_invite_num = $use_invite_num - $share_count > 0 ? $use_invite_num - $share_count : $rec_count;
//            vd($left_invite_num);
            if($left_invite_num >= $val['invite_num']){
                $val->share_count = $val['invite_num'];
                $share_count += $val['invite_num'];
            } else {
                $val->share_count = $left_invite_num;
            }
        }

        //总人数
        $total_count = array_sum(array_column($list,'invite_num'));
        $width_per = $total_count > 0 ? round(($rec_count/$total_count),2): 0;
        return ['list' => $list, 'rec_count' => $rec_count, 'total_count' => $total_count, 'width_per' => $width_per, 'share_user_id' => $this->getUserId()];
    }

    /**
     * 获取推荐总人数
     * @param int $rec_user_id
     * @return int
     */
    public function getShareTotalCount(int $rec_user_id){
        $userShareModel = new \addons\xilufitness\model\UserShareRecord;
        //推荐人数
        $rec_count = $userShareModel
            ->where(['brand_id' => $this->brand_id, 'rec_user_id' => $rec_user_id])
            ->count('*');
        return $rec_count;
    }


}