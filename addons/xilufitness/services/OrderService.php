<?php


namespace addons\xilufitness\services;


use addons\xilufitness\model\CardCategory;
use addons\xilufitness\model\CardPackage;
use addons\xilufitness\services\pay\PayService;
use addons\xilufitness\services\recharge\RechargeService;
use think\Db;
use think\Exception;
use think\exception\DbException;

class OrderService extends BaseService
{

    /**
     * 订单创建
     * @param int $id 课程/活动/充值类型id/会员卡类型id/会员卡套餐ID
     * @param int $is_type 类型 1 团课 2 私教 3 活动 4 购买会员卡 5 购买会员卡套餐 0 充值
     * @param int $num 数量
     * @param int $pay_type 支付方式 0 免支付 1 余额支付 2 微信支付
     * @param int $coupon_id 优惠券id
     * @throws DbException
     */
    public function createOrder(int $id, int $is_type, int $num, int $pay_type, int $coupon_id){
        $model = new \addons\xilufitness\model\Order;
        if($is_type == 0){
            $goodsInfo = RechargeService::getInstance()->getRechargeDetail($id);
            $sale_price = $goodsInfo['recharge_amount'] ?? 0;
        }
        if(in_array($is_type,[1,2])){
            $goodsResult = CourseService::getInstance()->getDetail($id);
            $goodsInfo = $goodsResult['info'] ?? [];
            if(empty($goodsInfo)){
                $this->resultError( '课程不存在');
            }
            $sale_price = $goodsInfo['course_price'] ?? 0;
            $workCourseInfo = Db::name('xilufitness_work_course')->where(['id' => $id])->field(['start_at','end_at'])->find();
            $start_at =$workCourseInfo['start_at'];
            $data['course_camp_id'] = $goodsInfo['course_id'] ?? 0;
            $data['starttime'] = $start_at;
            $data['endtime'] = $workCourseInfo['end_at'];
            if($goodsInfo['is_plan'] == 4){
                $this->resultError('已售罄');
            } elseif ($goodsInfo['is_plan'] == 3){
                $this->resultError('已开始');
            }
        }
        if($is_type == 3) {
            $goodsResult = CourseService::getInstance()->getCampDetail($id);
            $goodsInfo = $goodsResult['info'] ?? [];
            if(empty($goodsInfo)){
                $this->resultError('活动不存在');
            }
            $sale_price = $goodsInfo['camp_price'] ?? 0;
            $workCampInfo = Db::name('xilufitness_work_camp')->where(['id' => $id])->field(['start_at','end_at'])->find();
            $start_at = $workCampInfo['end_at'];
            $data['course_camp_id'] = $goodsInfo['camp_id'] ?? 0;
            $data['starttime'] = $workCampInfo['start_at'];
            $data['endtime'] = $workCampInfo['end_at'];
            $signResult = CourseService::getInstance()->getSignList(xilufitness_get_id_value($goodsInfo['id']),3);
            if($signResult['user_count'] >= $goodsInfo['total_count']){
                $this->resultError('该活动人数已满，下次再来报名吧！');
            }
        }

        if(in_array($is_type,[1,2,3])){
            $orderInfo = (new \addons\xilufitness\model\Order())
                ->where(['user_id' => $this->getUserId(), 'order_type' => $is_type, 'data_id' => $id ?? 0,
                    'pay_status' => 1, 'order_status' => ['neq',4]])
                ->find();
            if(!empty($orderInfo)){
                $this->resultError('您已报名过了');
            }

            if(time() > $start_at){
                $this->resultError('报名已结束');
            }
            if(isset($goodsInfo['user_signed']) && $goodsInfo['user_signed'] == 1){
                $this->resultError('您已报名过了');
            }
        }

        if($is_type == 4) {
            $goodsInfo = CardCategory::get($id);
            $sale_price = $goodsInfo['cardprice'] ?? 0;
        }

        if($is_type == 5) {
            $goodsInfo = CardPackage::get($id);
            $sale_price = $goodsInfo['total_price'] ?? 0;
        }


        $couponDetail = CouponService::getInstance()->getUserCouponDetail($coupon_id);
        try {
            Db::startTrans();
            $total_amount = round(bcmul($sale_price,$num,2),2);
            $data['brand_id'] = $this->brand_id;
            $data['user_id'] = $this->getUserId();
            $data['shop_id'] = xilufitness_get_id_value($goodsInfo['shop']['id'] ?? 0);
            $data['coach_id'] = xilufitness_get_id_value($goodsInfo['coach_id'] ?? 0);
            $data['coupon_id'] = !empty($couponDetail) ? $coupon_id : 0;
            $data['data_id'] = $id;
            $data['order_no'] = xilufitness_build_order_no();
            $data['coupon_amount'] = $couponDetail['discount_amount'] ?? 0;
            $data['pay_amount'] = $total_amount - $data['coupon_amount'] > 0 ? round(bcsub($total_amount,$data['coupon_amount']),2) : 0;
            $data['total_amount'] = $is_type == 0 ? (round(bcmul($goodsInfo['account_amount'],$num,2),2) ?? 0) : $total_amount;
            $data['order_type'] = $is_type;
            if(isset($goodsInfo['is_plan']) && $goodsInfo['is_plan'] == 2){
                $data['order_status'] = 10;
            }
            if($pay_type == 1 && $data['pay_amount'] > $this->userInfo->account){
                throw new Exception('会员卡余额不足');
            }
            if($data['pay_amount'] <= 0){
                $pay_type = 0;
            }
            $data['pay_type'] = $pay_type;
            $result = $model->allowField(true)->save($data);
            if(false !== $result){
                $model->addGoods($model->id,$is_type,$num,$goodsInfo);
            }
            Db::commit();
        } catch (\Exception $e){
            Db::rollback();
            $this->resultError($e->getMessage());
        }

        if(false !== $result){
            return ['order_id' => $model->id, 'order_type' => $is_type, 'pay_type' => $pay_type ];
        }
        $this->resultError('下单失败');
    }

    /**
     * 订单列表
     * @param int $order_type 订单类型
     * @return array
     */
    public function getOrderList(int $order_type = 0){
        $model = new \addons\xilufitness\model\Order;
        $where['order.user_id'] = $this->getUserId();
        $where['order.brand_id'] = $this->brand_id;
        $where['order.order_type'] = $order_type;
        $where['order.pay_status'] = 1;
        $rows = $model
            ->field(['id','user_id','data_id','order_no','coupon_amount','pay_amount','total_amount','order_status','pay_type','order_type','pay_time','code_num','pay_status','endtime'])
            ->where($where)
            ->with(['goods' => function($query){
                return $query->withField(['id','order_id','goods_id','goods_name','thumb_image','sale_price','total_price','num','order_type']);
            },'shop' => function($query){
                return $query->withField(['id','shop_name','address','lat','lng']);
            }])
            ->order("id desc")
            ->paginate();
        return ['list' => $rows->items(), 'total_count' => $rows->total()];
    }

    /**
     * 获取订单详情
     * @param int $id 订单id
     * @retrun array
     * 订单状态 order_status 0 待支付 1 已支付  2 进行中 3 已完成 4 已取消 5 已过期 6 已评价 10 在排队
     */
    public function getOrderDetail(int $id){
        $model = new \addons\xilufitness\model\Order;
        $info = $model
            ->field(['id','brand_id','user_id','data_id','order_no','coupon_amount','pay_amount','total_amount','order_status','pay_type','order_type','pay_time',
                'code_num','pay_status','course_camp_id','shop_id','coach_id','endtime'])
            ->where(['id' => $id, 'brand_id' => $this->brand_id])
            ->find();
        if(!empty($info) && $info['order_type'] != 0 && $info['order_type'] != 4){
            $info->append(['course_camp']);
            if($info['order_type'] == 3){
                $courseCampResult = CourseService::getInstance()->getCampDetail($info->data_id ?? 0);
            } else {
                $courseCampResult = CourseService::getInstance()->getDetail($info->data_id ?? 0);
            }
            $info->course_camp = $courseCampResult['info'] ?? [];
        }
        //报名人数
        $userList = CourseService::getInstance()->getSignList(($info->data_id ?? 0),($info->order_type ?? -1));
        return ['info' => $info, 'userList' => $userList];
    }

    /**
     * 取消订单
     * @param int $id 订单id
     * @return array
     */
    public function cancelOrder(int $id){
        $infoResult = $this->getOrderDetail($id);
        if(empty($infoResult['info'])){
            $this->resultError('订单不存在');
        }
        if($infoResult['info']['pay_status'] != 1){
            $this->resultError('未支付订单，不支持取消');
        }
        if($infoResult['info']['order_type'] == 2){
            $this->resultError('私教课请联系客服线下退款');
        }
        if($infoResult['info']['order_type'] == 4){
            $this->resultError('购卡订单请联系客服线下退款');
        }
        if($infoResult['info']['order_type'] == 3){
            $start_at = Db::name('xilufitness_work_camp')->where(['id' => $infoResult['info']['data_id']])->value('start_at');
        } else {
            $start_at = Db::name('xilufitness_work_course')->where(['id' => $infoResult['info']['data_id']])->value('start_at');
        }
        if(time() > $start_at){
            $this->resultError('已过期，暂时不能取消');
        }
        if(time() > $start_at-(6*3600)){
            $this->resultError('您已超过退款时间,开课前6小时可退款');
        }
        if($infoResult['info']['pay_amount'] <= 0){
            $this->resultError('你未实际支付，暂时不能取消');
        }
        try {
            Db::startTrans();
            $result = $infoResult['info']->allowField(true)->save(['order_status' => 4]);
            if(false !== $result){
                //退款
                PayService::getInstance(['mini_config' => $this->data['mini_config']])->refundOrder(xilufitness_get_id_value($infoResult['info']['id']));
                //有排队释放
                PayService::getInstance(['mini_config' => $this->data['mini_config']])->orderQueue(1,$infoResult['info']['data_id'] ?? 0,$infoResult['order_type'] ?? -1,$this->brand_id);
            }
            Db::commit();
        } catch (\Exception $e){
            Db::rollback();
            $this->resultError($e->getMessage());
        }
        return ['code' => false !== $result ? 1 : 0, 'msg' => false !== $result ? '取消成功' : '取消失败'];
    }

    /**
     * 订单核销
     * @param int $id 订单id
     * @return array
     */
    public function confirmOrder(int $id = 0){
        $recordsModel = new \addons\xilufitness\model\OrderVerificationRecords;
        $info = $this->getOrderDetail($id);
        $orderInfo = $info['info'] ?? [];
        $coachInfo = CoachService::getInstance()->getCoachInfo(0);
        $day_start_at = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $day_end_at = mktime(23,59,59,date('m'),date('d'),date('Y'));
        $planInfo = null;
        if(empty($orderInfo)){
            $this->resultError('订单不存在');
        }
        if(!in_array($orderInfo['order_status'],[1,2]) || $orderInfo['order_type'] == 0 || $orderInfo['order_type'] == 4){
            $this->resultError('该订单状态不能核销');
        }
        if(empty($coachInfo['info'])){
            $this->resultError('教练不存在');
        }
        $coach_id =  xilufitness_get_id_value($coachInfo['info']['id'] ?? 0);
        $order_coach_id = xilufitness_get_id_value($info['info']['course_camp']['coach']['id'] ?? 0);
        if($coach_id != $order_coach_id){
            $this->resultError('该订单您核销不了，不是您的课程订单');
        }
        if($orderInfo['order_type'] != 3){
            //团课 私教
            $recordsCount = $recordsModel->where([
                'order_id' => xilufitness_get_id_value($orderInfo['id'] ?? 0),
                'check_time' => [['egt',$day_start_at],['elt',$day_end_at]]
            ])->count('*');
            $start_at = strtotime(date('Y-m-d',$orderInfo['course_camp']['class_time']).' '.$orderInfo['course_camp']['start_at']);
            $end_at = $orderInfo['course_camp']->getData('end_at');
            if($recordsCount >= $orderInfo['goods_num']){
                $this->resultError('超过人数上线，暂时不能核销');
            }
        } else {
            //活动
            $planModel = new \addons\xilufitness\model\WorkCampPlan;
            $planInfo = $planModel->where(['work_camp_id' => $orderInfo['data_id'],'day_date' => strtotime(date('Y-m-d',time())), 'day_end_at' => ['egt',date('H:i')] ])->find();
            if(empty($planInfo)){
                $this->resultError('未查询到上课计划,暂时不能核销');
            }
            $start_at = strtotime(date('Y-m-d',$planInfo->getData('day_date')).' '.$planInfo['day_start_at']);
            $end_at = strtotime(date('Y-m-d',$planInfo->getData('day_date')).' '.$planInfo['day_end_at']);
            $recordsCount = $recordsModel->where([
                'order_id' => xilufitness_get_id_value($orderInfo['id'] ?? 0),
                'check_time' => [['egt',$start_at],['elt',$end_at]]
            ])->count('*');
            if($recordsCount >= $orderInfo['goods_num']){
                $this->resultError('超过人数上线，暂时不能核销');
            }
        }
        if($start_at > time()){
            $this->resultError('课程还未开始，不能核销');
        }

        try {
            Db::startTrans();
            $result = $recordsModel->allowField(true)->save([
                'brand_id' => $this->brand_id,
                'order_id' =>  xilufitness_get_id_value($orderInfo['id'] ?? 0),
                'coach_id' => $order_coach_id,
                'shop_id' => $orderInfo['shop_id'] ?? 0,
                'data_id' => $orderInfo['data_id'] ?? 0,
                'user_id' => $orderInfo['user_id'] ?? 0,
                'check_time' => time()
            ]);
            if(false !== $result){
                //团课 私教课 直接完成
                if($orderInfo['order_type'] != 3){
                    $orderInfo->allowField(true)->save(['order_status' => 3]);
                    $orderInfo->setInc('code_num',1);
                } else {
                    if($orderInfo['code_num'] == $orderInfo['code_total_num'] - 1){
                        $orderInfo->allowField(true)->save(['order_status' => 3]);
                    }
                    $orderInfo->setInc('code_num',1);
                }

                //教练统计信息
                $this->getCoachStatics($orderInfo,$order_coach_id,$recordsModel,$planInfo);
                //教练入账
                $this->coachAccountChange($orderInfo,$order_coach_id);
                //学生统计
                $this->getStudentStatics($orderInfo,$recordsModel,$planInfo);
            }
            Db::commit();
        } catch (\Exception $e){
            Db::rollback();
            $this->resultError($e->getMessage());
        }


        if(false !== $result){
           return ['code' => 1];
        } else {
            return ['code' => 0, 'msg' => '核销失败'];
        }

       
    }

    /**
     * 获取上课时长
     * @param $orderInfo
     * @param $planInfo
     */
    public function getClassDuration($orderInfo,$planInfo){
        if($orderInfo['order_type'] != 3){
            $start_at = $orderInfo['course_camp']->getData('start_at');
            $end_at = $orderInfo['course_camp']->getData('end_at');
            $class_duration = ($end_at-$start_at)/60;
        } else {
            $start_at = strtotime(date('Y-m-d',$planInfo->getData('day_date')).' '.$planInfo['day_start_at']);
            $end_at = strtotime(date('Y-m-d',$planInfo->getData('day_date')).' '.$planInfo['day_end_at']);
            $class_duration = ($end_at-$start_at)/60;
        }
        return intval($class_duration);
    }

    /**
     * 统计学生训练天数，时长，上课次数
     * @param $orderInfo 订单信息
     * @param $recordsModel 核销记录模型
     * @param $planInfo 活动排课计划
     */
    public function getStudentStatics($orderInfo,$recordsModel,$planInfo){
        $userModel = new \addons\xilufitness\model\User;
        $userInfo = $userModel->where(['id' => $orderInfo['user_id'] ?? 0 ])->find();
        if(empty($userInfo)){
            return false;
        } else {
            $start_at = mktime(0,0,0,date('m'),date('d'),date('Y'));
            $end_at =  mktime(23,59,59,date('m'),date('d'),date('Y'));
            $class_duration = $this->getClassDuration($orderInfo,$planInfo);
            $recordsCount = $recordsModel->where([
                'user_id' => xilufitness_get_id_value($orderInfo['user_id']),
                'brand_id' => $this->brand_id,
                'createtime' => [['egt',$start_at],['elt',$end_at]]
            ])->count('*');
            if($recordsCount == 1){
                $userInfo->allowField(true)->save(['train_day' => ($userInfo['train_day'] + 1)]);
            }
            $userInfo->setInc('train_duration',$class_duration);
            $userInfo->setInc('train_count',1);

            //解锁勋章
            $mediaData['user_id'] = $orderInfo['user_id'] ?? 0;
            $mediaData['brand_id'] = $orderInfo['brand_id'] ?? 0;
            \think\Hook::listen('xilufitness_medal_unlocking',$mediaData);
        }

    }



    /**
     * 统计教练上课数，时长，上课人数
     * @param $orderInfo 订单信息
     * @param $coach_id 教练id
     * @param $recordsModel 核销记录模型
     *  @param $planInfo 活动排课计划
     */
    public function getCoachStatics($orderInfo,$coach_id,$recordsModel,$planInfo){
        $coachModel = new \addons\xilufitness\model\CoachAccount;
        $coachInfo = $coachModel->where(['coach_id' => $coach_id, 'brand_id' => $this->brand_id ])->find();
        $class_duration = $this->getClassDuration($orderInfo,$planInfo);
        $start_at = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $end_at =  mktime(23,59,59,date('m'),date('d'),date('Y'));
        $recordCount = $recordsModel->where([
            'brand_id' => $this->brand_id,
            'coach_id' => $coach_id,
            'data_id' => $orderInfo['data_id'],
            'createtime' => [['egt',$start_at],['elt',$end_at]]
        ])->count('*');
        if($recordCount == 1){
            $coachInfo->allowField(true)->save(['course_count' => ($coachInfo['course_count'] + 1), 'class_duration' => ($coachInfo['class_duration']) + $class_duration]);
        }
        $coachInfo->setInc('course_total_count',1);
    }

    /**
     * 教练账户变动
     * @param $orderInfo  订单信息
     * @param $coach_id 教练id
     */
    public function coachAccountChange($orderInfo,$coach_id){
        CoachService::getInstance()->createAccount($orderInfo['brand_id'],$coach_id);
        $model = new \addons\xilufitness\model\CoachCash;
        $data['coach_id'] = $coach_id;
        $data['brand_id'] = $orderInfo['brand_id'];
        $data['title'] = $orderInfo['order_type'] != 3 ? ($orderInfo['course_camp']['course']['title'] ?? '') : ($orderInfo['course_camp']['camp']['title'] ?? '');
        $data['is_type'] = $orderInfo['order_type'];
        $data['data_id'] = xilufitness_get_id_value($orderInfo['id']);
        $data['cash_type'] = 1;
        $data['cash_price'] = $orderInfo['course_camp']['write_off_price'];
        $model->allowField(true)->save($data);
    }

    /**
     * 订单评论
     * @param int $order_id 订单id
     * @param int $profession_star 专业度
     * @param int $affinity_star 亲和力
     * @param int $impression_star 印象
     * @param string $content 评论内容
     * @return array
     */
    public function addOrderComment(int $order_id, int $profession_star, int $affinity_star, int $impression_star, string $content){
        $info = $this->getOrderDetail($order_id)['info'] ?? '';
        $model = new \addons\xilufitness\model\OrderComment;
        $commentExist = $model
            ->where(['order_id' => $order_id, 'user_id' => $this->getUserId(), 'course_camp_id' => $info['course_camp_id'] ?? 0])
            ->field(['id'])
            ->find();
        if(empty($info)){
            $this->resultError('订单不存在');
        } elseif($info['order_status'] != 3){
            $this->resultError('该状态不能评价，请先核销后在评价');
        } elseif (!empty($commentExist)){
            $this->resultError('已评价过了，无需在评');
        } else {

            try {
                Db::startTrans();
                $data['user_id'] = $this->getUserId();
                $data['brand_id'] = $this->brand_id;
                $data['shop_id'] = $info['shop_id'] ?? 0;
                $data['coach_id'] = $info['coach_id'] ?? 0;
                $data['course_camp_id'] = $info['course_camp_id'] ?? 0;
                $data['order_id'] = $order_id;
                $data['profession_star'] = $profession_star;
                $data['affinity_star'] = $affinity_star;
                $data['impression_star'] = $impression_star;
                $data['content'] = $content;
                $data['star'] = round(($profession_star+$affinity_star+$impression_star)/3,0);
                $data['course_type'] = $info['order_type'] ?? 0;
                $result = $model->allowField(true)->save($data);
                $info->allowField(true)->save(['order_status' => 6]);
                Db::commit();
            } catch (\Exception $e){
                Db::rollback();
                $this->resultError($e->getMessage() ?? '评论失败');
            }
            if(false !== $result){
                return ['code' => 1];
            } else {
                return ['code' => 0, 'msg' => '评论失败'];
            }

        }
    }


}