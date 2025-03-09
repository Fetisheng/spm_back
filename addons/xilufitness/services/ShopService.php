<?php


namespace addons\xilufitness\services;


use addons\xilufitness\services\user\UserService;

class ShopService extends BaseService
{

    protected $model = null;

    protected function __initialize()
    {
        $this->model = new \addons\xilufitness\model\Shop;
    }

    /**
     * 获取门店列表
     * @param int $page 分页码
     * @param string $lat 纬度
     * @param string $lng 经度
     * @param int $city_id 城市id
     * @param string $keyword 关键词
     */
    public function getShopList(string $lat, string $lng, int $city_id = 0, string $keyword=''){
        $where['brand_id'] = $this->brand_id;
        if(!empty($city_id)){
            $where['city_id'] = $city_id;
        }
        if(!empty($keyword)){
            $where['shop_name'] = ['like','%'.$keyword.'%'];
        }
        $rows = $this->model
            ->normal()
            ->where($where)
            ->field(['id','shop_name','shop_image','address','lat','lng',
                "(6378.138 * 2 * asin(sqrt(pow(sin((lat * pi() / 180 - ".$lat." * pi() / 180) / 2),2) + cos(lat * pi() / 180) * cos(".$lat." * pi() / 180) * pow(sin((lng * pi() / 180 - ".$lng." * pi() / 180) / 2),2))) * 1000) as distance"])
            ->order("distance asc")
            ->paginate();
        return ['list' => $rows->items(), 'total_count' => $rows->total() ];
    }

    /**
     * 获取门店详情
     * @param int $id 门店id
     */
    public function getDetail(int $id){
        $info = $this->model
            ->where(['id' => $id, 'brand_id' => $this->brand_id])
            ->field(['id','shop_name','shop_image','address','lat','lng','shop_images','star'])
            ->find();
        $isCollect = UserService::getInstance()->getCollectExists($id,1);
        $info['is_collect'] = !empty($isCollect) ? 1 : 0;
        return ['info' => $info];
    }

    /**
     * 获取门店课程数据
     * @param int $id 门店id
     * @param int $course_type 课程类型 1 团课 2 私教
     */
    public function getCourse(int $id,int $course_type = 1){
        $workCourseModel    = new \addons\xilufitness\model\WorkCourse;
        $list = $workCourseModel
            ->field(['id','course_id','start_at','end_at','course_price','market_price','sign_count','wait_count','course_type'])
            ->with(['course'  => function($query){
            return $query->withField(['id','title','thumb_image','lable_ids']);
            },'coach' => function($query){
                return $query->withField(['id','coach_name','coach_avatar','coach_group_id','lable_ids']);
            }])
            ->where(['work_course.brand_id' => $this->brand_id,'end_at' => ['gt',time()], 'shop_id' => $id, 'work_course.course_type' => $course_type ])
            ->select();
        return ['list' => $list];
    }

    /**
     * 获取门店教练数据
     * @param int $id 门店id
     */
    public function getCoach(int $id){
        $model = new \addons\xilufitness\model\Coach;
        $list = $model
            ->normal()
            ->where(['brand_id' => $this->brand_id])
            ->whereRaw("FIND_IN_SET({$id},`shop_ids`)")
            ->field(['id','coach_group_id','coach_name','coach_avatar', 'lable_ids'])
            ->select();
        return ['list' => $list];
    }

    /**
     * 获取门店活动数据
     * @param int $id 门店id
     */
    public function getCamp(int $id){
        $model = new \addons\xilufitness\model\WorkCamp;
        $list = $model
            ->field(['id','camp_id','start_at','end_at','camp_price','market_price','class_count','class_duration','camp_count','total_count'])
            ->with(['camp'  => function($query){
                return $query->withField(['id','title','thumb_image','lable_ids']);
            }])
            ->where(['work_camp.brand_id' => $this->brand_id,'end_at' => ['gt',time()], 'shop_id' => $id, 'work_camp.status' => 'normal' ])
            ->select();
        return ['list' => $list];
    }



}