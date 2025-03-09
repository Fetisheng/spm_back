<?php


namespace addons\xilufitness\services;


use addons\xilufitness\model\WorkCamp;
use addons\xilufitness\model\WorkCourse;
use addons\xilufitness\services\user\UserService;

class CourseService extends BaseService
{

    /**
     * 获取课程分类
     * @param int $pid  父级id
     */
    public function getCateByPid(int $pid){
        $model = new \addons\xilufitness\model\CourseCate;
        $list = $model
            ->normal()
            ->where(['pid' => $pid, 'brand_id' => $this->brand_id])
            ->field(['id','cate_name'])
            ->select();
        return ['list' => $list];
    }

    /**
     * 课程列表
     * @param string $lat  纬度
     * @param string $lng  经度
     * @param int $course_type  课程类型 1 团课 2 私教 3 活动
     * @param int $city_id  城市id
     */
    public function getCourseList($lat,$lng,$course_type,$province_id,$city_id,$area_id,$cate_pid,$cate_id,$choose_date,$start_at,$end_at){
        $shopModel = new \addons\xilufitness\model\Shop;
        $workCourseModel = new WorkCourse();
        $workCampModel = new WorkCamp();
        $where['brand_id'] = $this->brand_id;
        if(!empty($province_id)){
            $where['province_id'] = $province_id;
        }
        if(!empty($city_id)){
            $where['city_id'] = $city_id;
        }
        if(!empty($area_id)){
            $where['area_id'] = $area_id;
        }
        $rows = $shopModel
            ->field(['id','shop_name','lat','lng','city_id','brand_id',
                "(6378.138 * 2 * asin(sqrt(pow(sin((lat * pi() / 180 - ".$lat." * pi() / 180) / 2),2) + cos(lat * pi() / 180) * cos(".$lat." * pi() / 180) * pow(sin((lng * pi() / 180 - ".$lng." * pi() / 180) / 2),2))) * 1000) as distance"])
            ->where($where)
            ->order("distance asc")
            ->paginate();
        $list = $rows->items();
        foreach ($list as $item){
            $map['shop_id'] =  xilufitness_get_id_value($item['id']);
            if($course_type != 3){
                $map['work_course.status'] = 'normal';
                $map['work_course.course_type'] =  $course_type;
            } else {
                $map['work_camp.status'] = 'normal';
            }
            if(!empty($choose_date) && empty($start_at) && empty($end_at)){
                if($course_type != 3){
                    $map['class_time'] = strtotime($choose_date);
                } else {
                    $map['work_camp.start_at'] = ['egt',strtotime($choose_date)];
                }
            } else if(!empty($start_at) && !empty($end_at)){

                $choose_date = !empty($choose_date) ? $choose_date : date('Y-m-d',time());
                if($course_type != 3){
                    $map['start_at'] = ['elt',strtotime($choose_date.' '.$start_at)];
                    $map['end_at'] = ['egt',strtotime($choose_date.' '.$end_at)];
                } else {
                    $map['work_camp.start_at'] = ['egt',strtotime($choose_date.' '.$start_at)];
                }
            } else {
                $map['end_at'] = ['egt',time()];
            }
            $item->append(['list']);
            if($course_type == 1){
                $lists = $workCourseModel
                    ->field(['id','course_id','class_time','start_at','end_at','course_price','market_price','sign_count','wait_count','course_type'])
                    ->with(['course' => function($query) use($cate_pid,$cate_id) {
                        $c_where = '';
                        if(!empty($cate_pid)){
                            $c_where .= 'course.course_cate_pid = '.$cate_pid;
                        }
                        if(!empty($cate_id)){
                            $c_where .= ' and course.course_cate_id = '.$cate_id;
                        }
                        if(!empty($c_where)){
                            return $query->where($c_where)->withField(['id','title','thumb_image','lable_ids']);
                        }
                        return $query->withField(['id','title','thumb_image','lable_ids']);

                    }])
                    ->where($map)
                    ->select();
            } elseif ($course_type == 2){
                $lists = $workCourseModel
                    ->field(['id','course_id','coach_id','class_time','start_at','end_at','course_price','market_price','sign_count','wait_count','course_type'])
                    ->with(['coach' => function($query){
                        return $query->withField(['id','coach_avatar']);
                    },'course' => function($query) use($cate_pid,$cate_id) {
                        $c_where = '';
                        if(!empty($cate_pid)){
                            $c_where .= 'course.course_cate_pid = '.$cate_pid;
                        }
                        if(!empty($cate_id)){
                            $c_where .= ' and course.course_cate_id = '.$cate_id;
                        }
                        if(!empty($c_where)){
                            return $query->where($c_where)->withField(['id','title','thumb_image','lable_ids']);
                        }
                        return $query->withField(['id','title','thumb_image','lable_ids']);
                    }])
                    ->where($map)
                    ->select();
            } elseif($course_type == 3) {
                $lists = $workCampModel
                    ->field(['id','shop_id','camp_id','start_at','end_at','camp_price','market_price','class_count','class_duration','camp_count','total_count','status'])
                    ->with(['camp'  => function($query){
                        return $query->withField(['id','title','thumb_image','lable_ids']);
                    }])
                    ->where($map)
                    ->select();
            }
            $item->list = $lists;
        }
        return ['list' => $list, 'total_count' => $rows->total() ];
    }

    /**
     * 获取收藏的教练
     * @param string $lat 纬度
     * @param string $lng 经度
     * @param int $province_id 省分id
     * @param int $city_id 市id
     * @param int $area_id 区域id
     */
    public function getCollectCoach($lat,$lng,$province_id,$city_id,$area_id){
        $collectModel = new \addons\xilufitness\model\UserCollect;
        $coachModel = new \addons\xilufitness\model\Coach;
        $where['shop.brand_id'] = $this->brand_id;
        $where['user_id'] = $this->getUserId();
        $where['is_type'] = 2;
        if(!empty($province_id)){
            $where['shop.province_id'] = $province_id;
        }
        if(!empty($city_id)){
            $where['shop.city_id'] = $city_id;
        }
        if(!empty($area_id)){
            $where['shop.area_id'] = $area_id;
        }
        $rows = $collectModel
            ->join("xilufitness_shop shop","shop_id = shop.id")
            ->field(['shop.id','shop.lat','shop.lng','shop.shop_name','shop.address','data_id as coach_id',
                "(6378.138 * 2 * asin(sqrt(pow(sin((shop.lat * pi() / 180 - ".$lat." * pi() / 180) / 2),2) + cos(shop.lat * pi() / 180) * cos(".$lat." * pi() / 180) * pow(sin((shop.lng * pi() / 180 - ".$lng." * pi() / 180) / 2),2))) * 1000) as distance"])
            ->where($where)
            ->paginate();
        $list = $rows->items();
        if(!empty($list)){
            foreach ($list as $key => $val){
                $list[$key]['coach_list'] = $coachModel
                    ->where(['id' => $val['coach_id'] ?? 0])
                    ->field(['id','coach_name','coach_avatar','coach_group_id','lable_ids'])
                    ->select();
            }
        }
        return ['list' => $list, 'total_count' => $rows->total() ];
    }

    /**
     * 课程详情
     * @param int $id 课程id
     */
    public function getDetail(int $id){
        $model = new \addons\xilufitness\model\WorkCourse;
        $info = $model
            ->field(['id','course_id','coach_id','course_type','class_time','start_at','end_at','course_price',
                'market_price','write_off_price','sign_count','wait_count','class_count'])
            ->with(['course' => function($query){
                return $query->withField(['id','title','thumb_images','lable_ids','content','tip_content','thumb_image']);
            },'coach' => function($query){
                return $query->withField(['id','coach_name','coach_group_id','coach_avatar','lable_ids']);
            },'shop' => function($query){
                return $query->withField(['id','shop_name','address','lat','lng']);
            }])
            ->where(['work_course.id' => $id, 'work_course.status' => ['neq','hidden'], 'work_course.brand_id' => $this->brand_id])
            ->find();
        //报名人数
        $userList = CourseService::getInstance()->getSignList(xilufitness_get_id_value($info->id ?? 0),($info->course_type ?? -1));
        if(!empty($info)){
            $info->append(['user_signed']);
            $is_sign = $this->getSignData($id,$this->getUserId(),$info['course_type'] ?? -1);
            $info->user_signed = $is_sign;
        }
        return ['info' => $info, 'userList' => $userList];
    }

    /**
     * 判断是否已报名
     * @param int $id 课程/活动排课id
     * @param int $user_id 用户id
     */
    public function getSignData(int $id = 0,int $user_id = 0, int $course_type=-1){
        $orderModel = new \addons\xilufitness\model\Order;
        $orderSign = $orderModel
            ->where(['data_id' => $id, 'user_id' => $user_id, 'pay_status' => 1, 'order_status' => ['neq',4], 'order_type' => $course_type ])
            ->find();
        return !empty($orderSign) ? 1 : 0;
    }

    /**
     * 获取排课时间
     * 默认当前时间往后一周
     */
    public function getClassTime(){
        $start_at = time();
        $timeList = [];
        $week = (new \addons\xilufitness\model\WorkCourse)->getWeek();
        for ($i = 0; $i<=6; ++$i){
            $timeList[$i] = [
                'week' => ($i == 0 ? '今' : ($i == 1 ? '明': $week[date('w',$i == 0 ? $start_at : strtotime("+$i days",$start_at))])),
                'day' => date('d',$i == 0 ? $start_at : strtotime("+$i days",$start_at)),
                'day_date' => date('Y-m-d',$i == 0 ? $start_at : strtotime("+$i days",$start_at)),
            ];
        }
        return ['list' => $timeList];
    }

    /**
     * 获取时间段
     */
    public function getTimeList(){
        $list = [
            0 => [
                'title' => '6点-9点',
                'start_at' => '06:00:00',
                'end_at'   => '09:00:00'
            ],
            1 => [
                'title' => '9点-12点',
                'start_at' => '09:00:00',
                'end_at'   => '12:00:00'
            ],
            2 => [
                'title' => '12点-14点',
                'start_at' => '12:00:00',
                'end_at'   => '14:00:00'
            ],
            3 => [
                'title' => '14点-18点',
                'start_at' => '14:00:00',
                'end_at'   => '18:00:00'
            ],
            4 => [
                'title' => '18点-24点',
                'start_at' => '18:00:00',
                'end_at'   => '14:00:00'
            ]
        ];
        return $list;
    }

    /**
     * 获取活动详情
     * @param int $id 活动id
     * @return array
     */
    public function getCampDetail(int $id){
        $model = new \addons\xilufitness\model\WorkCamp;
        $info = $model
            ->field(['id','camp_id','coach_id','shop_id','start_at','end_at','camp_price','write_off_price','market_price','class_count','class_duration','camp_count','sign_count','total_count','description'])
            ->with(['camp'  => function($query){
                return $query->withField(['id','title','thumb_image','lable_ids','thumb_images','content','tip_content','description']);
            }, 'shop' => function($query){
                return $query->withField(['id','shop_name','address','lat','lng']);
            }, 'coach' => function($query){
                return $query->withField(['id','coach_name','coach_avatar','lable_ids','coach_group_id']);
            }])
            ->where(['work_camp.brand_id' => $this->brand_id,'work_camp.id' => $id, 'work_camp.status' => ['neq','hidden'] ])
            ->find();
        if(!empty($info)){
            $info->append(['current_camp_list']);
            $current_camp_list = $model
                ->field(['id','camp_id','shop_id','start_at','end_at','camp_price','market_price','class_count','class_duration','camp_count','sign_count','total_count','description'])
                ->with(['camp'  => function($query){
                    return $query->withField(['id','title','thumb_image','lable_ids','thumb_images','content','tip_content','description']);
                }, 'shop' => function($query){
                    return $query->withField(['id','shop_name','address','lat','lng']);
                }, 'coach' => function($query){
                    return $query->withField(['id','coach_name','coach_avatar','lable_ids','coach_group_id']);
                }])
                ->where(['work_camp.brand_id' => $this->brand_id,'work_camp.camp_id' => $info->camp_id ?? 0, 'work_camp.status' => 'normal', 'work_camp.end_at' => ['egt',time()], 'work_camp.shop_id' => $info->shop_id ?? 0 ])
                ->select();
            foreach ($current_camp_list as $key => $val){
                $current_camp_list[$key]['user_signed'] = $this->getSignData(xilufitness_get_id_value($val['id']),$this->getUserId(),3);;
            }
            $info->current_camp_list = $current_camp_list;
        }
        //报名人数
        $userList = CourseService::getInstance()->getSignList(xilufitness_get_id_value($info->id ?? 0),3);
        return ['info' => $info, 'userList' => $userList];
    }

    /**
     * 获取本期报名人数列表
     * @param int $is_type 类型 1 团课 2 私教 3 活动
     * @param int $id 课程排课/活动排课/id
     * @return array
     */
    public function getSignList(int $id, int $is_type){
        $model = new \addons\xilufitness\model\Order;
        $list = $model
            ->field(['id','user_id','data_id'])
            ->with(['user' => function($query){
                return $query->withField(['id','nickname','avatar','train_day','train_duration','train_count']);
            }])
            ->where(['data_id' => $id, 'order_type' => $is_type, 'pay_status' => 1, 'order_status' => ['neq',4] ])
            ->select();
        $user_count = array_sum(array_column($list,'goods_num'));
        return ['list' => $list, 'user_count' => $user_count];
    }

    /**
     * 获取评价列表
     * @param int $id 课程id
     * @param int $course_type 课程类型 1 团课 2 私教课 3 活动
     * @param int $shop_id 门店id
     * @return array
     */
    public function getCommentList($id,$course_type,$shop_id){
        $model = new \addons\xilufitness\model\OrderComment;
        $where['order_comment.brand_id'] = $this->brand_id;
        if(!empty($id)){
            $where['order_comment.course_camp_id'] = $id;
        }
        if(!empty($course_type)){
            $where['order_comment.course_type'] = $course_type;
        }
        if(!empty($shop_id)){
            $where['order_comment.shop_id'] = $shop_id;
        }
        $rows = $model
            ->with(['user' => function($query){
                return $query->withField(['id','avatar','nickname','train_duration']);
            }])
            ->where($where)
            ->field(['order_comment.id','order_comment.user_id','order_comment.star','order_comment.content','order_comment.createtime'])
            ->order("order_comment.createtime desc")
            ->paginate();
        return ['list' => $rows->items(), 'total_count' => $rows->total()];
    }


}