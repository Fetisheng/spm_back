<?php
namespace addons\xilufitness\services\index;

use addons\xilufitness\services\BaseService;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Log;

class IndexService extends BaseService
{

    /**
     * 获取首页数据
     * @param string $lat 纬度
     * @param string $lng 经度
     * @param int $brand_id 品牌id
     * @param int $city_id 城市id
     * @param int $province_id 省分id
     */
    public function getHomeData(string $lat, string $lng,int $city_id=0,int $province_id=0){
        $bannerModel        = new \addons\xilufitness\model\Banner;
        $shopModel          = new \addons\xilufitness\model\Shop;
        $workCourseModel    = new \addons\xilufitness\model\WorkCourse;
        $areaModel          = new \addons\xilufitness\model\Area;
        $brand_id           = $this->brand_id ?? 0;
        //定位的城市 信息
        $locationInfo = xilufitnessGetCityByLatLng($lat ?? '31.231859',$lng ?? '121.486561');
        Log::log($locationInfo);
        $city_info = $areaModel->where(['name' => $locationInfo['city'] ?? '上海市' ])->field(['id','name'])->find();
        Log::log($city_info);
        //banner数据
        $bannerList = $bannerModel->getBannerList($brand_id,1);
        Log::log($bannerList);

        //门店数据
        $s_where['brand_id'] = $brand_id;
        if(!empty($province_id)){
            $s_where['province_id'] = $province_id;
        }
        if(!empty($city_id)){
            $s_where['city_id'] = $city_id;
        } else {
            $s_where['city_id'] = xilufitness_get_id_value($city_info['id'] ?? 0);
        }

        $shopList = $shopModel::normal()
            ->where($s_where)
            ->field(['id','shop_name','shop_image','address','lat','lng',
                "(6378.138 * 2 * asin(sqrt(pow(sin((lat * pi() / 180 - ".$lat." * pi() / 180) / 2),2) + cos(lat * pi() / 180) * cos(".$lat." * pi() / 180) * pow(sin((lng * pi() / 180 - ".$lng." * pi() / 180) / 2),2))) * 1000) as distance"])
            ->order("distance asc")
            ->limit(20)
            ->select();
        Log::log($shopList);
        //课程数据
        $c_where['work_course.brand_id'] = $brand_id;
        $c_where['end_at'] = ['gt',time()];
        if(!empty($province_id)){
            $c_where['shop.province_id'] = $province_id;
        }
        if(!empty($city_id)){
            $c_where['shop.city_id'] = $city_id;
        } else {
            $c_where['shop.city_id'] = xilufitness_get_id_value($city_info['id'] ?? 0);
        }
        $courseList = $workCourseModel
            ->normal()
            ->field(['id','course_id','start_at','end_at','course_price','market_price','sign_count','wait_count','course_type'])
            ->with(['course' => function($query){
                return $query->withField(['id','title','thumb_image','lable_ids']);
            }, 'shop' => function($query){
                return $query->withField(['id','shop_name','shop_image']);
            }])
            ->where($c_where)
            ->limit(20)
            ->group('course_id')
            ->select();
        Log::log($courseList);
        return [
            'bannerList' => $bannerList,
            'shopList' => $shopList,
            'courseList' => $courseList,
            'cityInfo' => ['id' => $city_info['id'], 'name' => $city_info['name'] ]
        ];
    }

    /**
     * 获取城市数据
     * @param int $pid 父级id
     * @return array
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public function getCityList(int $pid){
        $model = new \addons\xilufitness\model\Area;
        $list = $model
            ->where(['pid' => $pid])
            ->field(['id','name','pid'])
            ->select();
        if($pid == 0 && !empty($list)){
            foreach ($list as $key => $val){
                $city_province = ['北京','天津','上海','重庆','深圳'];
                if(in_array($val['name'],$city_province)){
                    $cityInfo = $model->where(['pid' => xilufitness_get_id_value($val['id'])])->field(['id','name','pid'])->find();
                    $cityInfo['province_id'] = $val['id'];
                    $list[$key] = $cityInfo;
                    unset($cityInfo);
                }
            }
        }
        return ['list' => $list ];
    }

}