<?php


namespace addons\xilufitness\services;


use addons\xilufitness\services\user\UserService;
use addons\xilufitness\validate\CoachReport;
use think\Db;

class CoachService extends BaseService
{

    /**
     * 教练列表
     * @param string $lat
     * @param string $lng
     * @return array
     */
    public function getCoachList(string $lat,string $lng,int $cate_pid,int $cate_id, string $keywords,int $city_id=0,int $province_id = 0, int $area_id = 0){
        $shopModel = new \addons\xilufitness\model\Shop;
        $where['brand_id'] = $this->brand_id;
        if(!empty($cate_pid) || !empty($cate_id) || !empty($keywords)){
            $workCourseModel = new \addons\xilufitness\model\WorkCourse;
            $courseWhere['work_course.brand_id'] = $this->brand_id;
            if(!empty($cate_pid)){
                $courseWhere['course.course_cate_pid'] = $cate_pid;
            }
            if(!empty($cate_id)){
                $courseWhere['course.course_cate_id'] = $cate_id;
            }
            if(!empty($keywords)){
                $courseWhere['course.title|coach.coach_name'] = ['like','%'.$keywords.'%'];
            }
            $courseList = $workCourseModel
                ->field(['shop_id','course_id','coach_id','end_at'])
                ->with(['course' => function($query){
                    return $query->withField(['id','title','course_cate_pid','course_cate_id']);
                },'coach' => function($query){
                    return $query->withField(['id','coach_name','coach_group_id']);
                }])
                ->where($courseWhere)
                ->select();

            $shop_ids = array_column($courseList,'shop_id');
            if(!empty($keywords) && empty($shop_ids)){
                $where['shop_name'] = ['like','%'.$keywords.'%'];
            } else {
                $where['id'] = ['in',$shop_ids ?? [-1] ];
            }

        }
        if(!empty($city_id)){
            $where['city_id'] = $city_id;
        }
        if(!empty($province_id)){
            $where['province_id'] = $province_id;
        }
        if(!empty($area_id)){
            $where['area_id'] = $area_id;
        }

        $rows = $shopModel
            ->normal()
            ->where($where)
            ->field(['id','shop_name','lat','lng',
                "(6378.138 * 2 * asin(sqrt(pow(sin((lat * pi() / 180 - ".$lat." * pi() / 180) / 2),2) + cos(lat * pi() / 180) * cos(".$lat." * pi() / 180) * pow(sin((lng * pi() / 180 - ".$lng." * pi() / 180) / 2),2))) * 1000) as distance"])
            ->order("distance asc")
            ->paginate();
        $list = $rows->items();
        foreach ($list as $key => $val){
            $result = ShopService::getInstance()->getCoach(xilufitness_get_id_value($val['id']));
            $list[$key]['coach_list'] = $result['list'] ?? [];
        }
        return ['list' => $list, 'total_count' => $rows->total()];

    }

    /**
     * 获取教练详情
     * @param int $id 教练id
     * @param int $shop_id 门店id
     * @return array
     */
    public function getDetail(int $id, int $shop_id){
        $model = new \addons\xilufitness\model\Coach;
        $shopModel = new \addons\xilufitness\model\Shop;
        $info = $model
            ->normal()
            ->field(['id','coach_name','coach_group_id','coach_avatar','coach_images','lable_ids','coach_info'])
            ->where(['id' => $id, 'brand_id' => $this->brand_id])
            ->find();
        if(!empty($info)){
            $info->append(['shop','is_collect']);
            $info->shop = $shopModel
                ->where(['id' => $shop_id])
                ->field(['id','shop_name','address','lat','lng'])
                ->find();
            $isCollect = UserService::getInstance()->getCollectExists($id,2,$shop_id);
            $info->is_collect = $isCollect;
        }
        return ['info' => $info];

    }

    /**
     * 获取教练课程/活动数据
     * @param int $id 教练id
     * @param int $shop_id 门店id
     * @param int $is_type 类型 1 团课 2 私教 3 活动
     * @return array
     */
    public function getList(int $id, int $shop_id, int $is_type){
        if($is_type == 3){
            $result = $this->getCampList($id,$shop_id);
        } else {
            $result = $this->getCourseList($id,$shop_id,$is_type);
        }
        return  $result;
    }

    /**
     * 获取教练课程
     * @param int $id 教练id
     * @param int $shop_id 门店id
     * @param int $is_type 类型 1 团课 2 私教
     * @return array
     */
    public function getCourseList(int $id, int $shop_id, int $is_type){
        $model = new \addons\xilufitness\model\WorkCourse;
        $list = $model
            ->field(['id','course_id','class_time','start_at','end_at','course_price','market_price','class_count','sign_count','wait_count','course_type'])
            ->with(['course' => function($query){
                return $query->withField(['id','title','thumb_image','lable_ids']);
            }])
            ->where(['work_course.coach_id' => $id, 'work_course.shop_id' => $shop_id,'work_course.status' => 'normal', 'work_course.course_type' => $is_type, 'end_at' => ['egt',time()]])
            ->select();
        return ['list' => $list];
    }

    /**
     * 获取活动
     * @param int $id 教练id
     * @param int $shop_id 门店id
     * @return array
     */
    public function getCampList(int $id, int $shop_id){
        $model = new \addons\xilufitness\model\WorkCamp;
        $list = $model
            ->field(['id','camp_id','start_at','end_at','camp_price','market_price','class_count','class_duration','camp_count','total_count'])
            ->with(['camp'  => function($query){
                return $query->withField(['id','title','thumb_image','lable_ids']);
            }])
            ->where(['work_camp.brand_id' => $this->brand_id,'end_at' => ['gt',time()], 'coach_id' => $id, 'work_camp.shop_id' => $shop_id ,'work_camp.status' => 'normal' ])
            ->select();
        return ['list' => $list];
    }

    /**
     * 获取教练个人信息
     * @return array
     */
    public function getCoachInfo(int $id = 0){
        if(empty($this->userInfo)){
            $this->result('请先登录','',401);
        }
        $coachModel = new \addons\xilufitness\model\Coach;
        $coachAccountModel = new \addons\xilufitness\model\CoachAccount;
        if(empty($id)){
            $mobile = $this->userInfo->getData('mobile');
            $where['coach_mobile'] = $mobile;
        } else {
            $where['id'] = $id;
        }

        $info = $coachModel
            ->field(['id','coach_name','coach_avatar','coach_group_id','lable_ids','coach_info'])
            ->where($where)
            ->find();
        if(!empty($info)){
            $info->append(['accountInfo']);
            $info->accountInfo = $coachAccountModel
                ->where(['brand_id' => $this->brand_id, 'coach_id' => xilufitness_get_id_value($info->id)])
                ->field(['id','course_count','class_duration','course_total_count','account','withdraw_account'])
                ->find();
            //创建账户
            $this->createAccount($this->brand_id,xilufitness_get_id_value($info->id));
        }

        return ['info' => $info, 'userInfo' => $this->userInfo];
    }

    /**
     * 提交报备
     * @param string $start_at 开始时间
     * @param string $end_at 结束时间
     * @param string $description 请假事由
     * @param int $id 教练id
     * @return array
     */
    public function addReport(int $id, string $start_at, string $end_at, string $description){
        $data['start_at'] = $start_at;
        $data['end_at'] = $end_at;
        $data['description'] = $description;
        $data['coach_id'] = $id;
        $data['brand_id'] = $this->brand_id;
        $reportValidate = \think\Loader::validate(CoachReport::class);
        $validateResult = $reportValidate->check($data);
        if(!$validateResult){
            $this->resultError($reportValidate->getError());
        }
        if(time() > strtotime($start_at)){
            $this->resultError('开始时间必须大于当前时间');
        }
        if(strtotime($end_at) < strtotime($start_at)){
            $this->resultError('开始时间不能大于结束时间');
        }
        $model = new \addons\xilufitness\model\CoachReport;
        try {
            Db::startTrans();
            $result = $model->allowField(true)->save($data);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->resultError($e->getMessage());
        }
        if(false !== $result){
            return ['code' => 1];
        } else {
            return ['code' => 0, 'msg' => '提交失败'];
        }
    }

    /**
     * 报备信息列表
     * @param int $page 分页码
     * @param int $id 教练id
     * @return array
     */
    public function getReportList(int $id){
        $model = new \addons\xilufitness\model\CoachReport;
        $rows = $model
            ->where(['coach_id' => $id, 'brand_id' => $this->brand_id])
            ->field(['id','start_at', 'end_at', 'description', 'report_status'])
            ->paginate();
        return ['list' => $rows->items(), 'total_count' => $rows->total() ];
    }

    /**
     * 收入明细列表
     * @param int $page 分页码
     * @param int $id 教练id
     * @return array
     */
    public function getCashList(int $id){
        $model = new \addons\xilufitness\model\CoachCash;
        $rows = $model
            ->where(['coach_id' => $id, 'brand_id' => $this->brand_id])
            ->field(['id','title','cash_price','cash_type','createtime'])
            ->order("id desc")
            ->paginate();
        return ['list' => $rows->items(), 'total_count' => $rows->total() ];
    }

    /**
     * 提交提现
     * @param int $id 教练id
     * @param float $price 提现金额
     * @return array
     */
    public function addWithdraw(int $id,float $price){
        $model = new \addons\xilufitness\model\CoachWithdraw;
        $coachResult = $this->getCoachInfo($id);
        if(empty($coachResult['info'])){
            $this->resultError('教练不存在');
        }
        if($coachResult['info']['accountInfo']['account'] < $price){
            $this->resultError('余额不足');
        }
        try {
            Db::startTrans();
            $data['coach_id'] = $id;
            $data['brand_id'] = $this->brand_id;
            $data['withdraw_price'] = $price;
            $data['order_no'] = xilufitness_build_order_no();
            $result = $model->allowField(true)->save($data);
            Db::commit();
        } catch (\Exception $e){
            Db::rollback();
            $this->resultError($e->getMessage());
        }
        return ['code' => false !== $result ? 1 : 0, 'msg' => false !== $result ? '提交成功' : '提交失败'];
    }

    /**
     * 获取排课列表
     * @param int $course_type 类型 1 团课 2 私教 3 活动
     * @param int $day_time 日期 时间戳
     * @return array
     */
    public function getScheduling(int $course_type = 1,int $day_time){
        $coachInfo = $this->getCoachInfo();
        if(!empty($coachInfo)){
            $id = xilufitness_get_id_value($coachInfo['info']['id'] ?? 0);
        }
        if($course_type == 3){
            $planModel = new \addons\xilufitness\model\WorkCampPlan;
            $rows = $planModel
                ->field(['id','work_camp_id','day_date','day_start_at', 'day_end_at'])
                ->where(['day_date' => $day_time, 'workCamp.brand_id' => $this->brand_id, 'workCamp.coach_id' => $id])
                ->with(['work_camp' => function($query){
                    return $query->withField(['id','brand_id','camp_id','shop_id','start_at','end_at']);
                }])
                ->paginate();
            $list = $rows->items();
            foreach ($list as $key => $val){
                $val->append(['camp','shop','user_count']);
                $campModel = new \addons\xilufitness\model\Camp;
                $shopModel = new \addons\xilufitness\model\Shop;
                $val->camp = $campModel->where(['id' => xilufitness_get_id_value($val['work_camp']['camp_id'])])->field(['id','title'])->find();
                $val->shop = $shopModel->where(['id' => xilufitness_get_id_value($val['work_camp']['shop_id'])])->field(['id','shop_name','address','lat','lng'])->find();
                $userListResult = CourseService::getInstance()->getSignList($val['work_camp_id'],3);
                $val->user_count = $userListResult['user_count'] ?? 0;
            }

        } else {
            $model = new \addons\xilufitness\model\WorkCourse;
            $rows = $model
                ->where(['work_course.brand_id' => $this->brand_id, 'coach_id' => $id, 'work_course.course_type' => $course_type, 'class_time' => $day_time, 'end_at' => ['egt', $day_time] ])
                ->field(['id','shop_id','course_id','course_type','class_time','start_at','end_at','sign_count','wait_count'])
                ->with(['shop' => function($query){
                    return $query->withField(['id','shop_name','address','lat','lng']);
                }, 'course' => function($query){
                    return $query->withField(['id','title']);
                }])
                ->paginate();
            $list = $rows->items();
            foreach ($list as $key => $val){
                $val->append(['user_count']);
                $userListResult = CourseService::getInstance()->getSignList(xilufitness_get_id_value($val['id']),$val['course_type']);
                $val->user_count = $userListResult['user_count'] ?? 0;
            }
        }
        return ['list' => $list, 'total_count' => $rows->total()];
    }

    /**
     * 排课详情
     * @param int $id 排课id
     * @param int $work_camp_id 活动排课id
     * @param int $is_type 类型 1 团课 2 私教 3 活动
     */
    public function getSchedulingDetail(int $id, int $work_camp_id, int $is_type=1){
        if($is_type == 3){
            $model = new \addons\xilufitness\model\WorkCamp;
            $planModel = new \addons\xilufitness\model\WorkCampPlan;
            $info = $model
                ->field(['id','camp_id','shop_id','start_at','end_at'])
                ->with(['shop' => function($query){
                    return $query->withField(['id','shop_name','address','lat','lng']);
                },'camp' => function($query){
                    return $query->withField(['id','title']);
                }])
                ->where(['work_camp.id' => $work_camp_id])
                ->find();
            if(!empty($info)){
                $info->append(['plan']);
                $info->plan = $planModel->where(['id' => $id])->field(['id','day_date','day_start_at','day_end_at'])->find();
            }
        } else {
            $model = new \addons\xilufitness\model\WorkCourse;
            $info = $model
                ->field(['id','course_id','shop_id','class_time','start_at','end_at','sign_count','wait_count'])
                ->with(['shop' => function($query){
                    return $query->withField(['id','shop_name','address','lat','lng']);
                },'course' => function($query){
                    return $query->withField(['id','title']);
                }])
                ->where(['work_course.id' => $id])
                ->find();
        }
        return ['info' => $info];
    }

    /**
     * 创建教练账户
     * @param int $brand_id 所属品牌商
     * @param int $coach_id 所属教练
     */
    public function createAccount(int $brand_id, int $coach_id){
        $model = new \addons\xilufitness\model\CoachAccount;
        $accountInfo = $model->where(['brand_id' => $brand_id,'coach_id' => $coach_id])->field(['id'])->find();
        if(empty($accountInfo)){
            //创建账户
            return $model->save([
                'brand_id' => $brand_id,
                'coach_id' => $coach_id
            ]);
        }
        return true;
    }

    /**
     * 获取学生排行榜
     * @param int $coach_id 教练id
     * @return array
     */
    public function getStudentRanking(int $coach_id){
        $model = new \addons\xilufitness\model\Order;
        $rows = $model
            ->field(['user_id','id','coach_id'])
            ->with(['user' => function($query){
                return $query->withField(['id','nickname','avatar','train_duration','mobile']);
            }])
            ->where(['coach_id' => $coach_id, 'pay_status' => 1])
            ->group('order.user_id')
            ->order("user.train_duration desc")
            ->paginate();
        return ['list' => $rows->items(), 'total_count' => $rows->total() ];

    }



}