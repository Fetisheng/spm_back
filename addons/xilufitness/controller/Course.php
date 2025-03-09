<?php


namespace addons\xilufitness\controller;

use addons\xilufitness\services\CourseService;

/**
 * @ApiSector(课程控制器)
 * @ApiRoute('addons/xilufitness/course')
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Course extends Base
{
    protected $noNeedLogin = '*';

    /**
     * @ApiTitle('课程数据获取')
     * @ApiSummary('轮播，课程数据获取')
     * @ApiRoute('addons/xilufitness/course/index')
     * @ApiMethod('GET')
     * @ApiParams(name='lat',type='string',required=true,description="纬度")
     * @ApiParams(name='lng',type='string',required=true,description="经度")
     * @ApiParams(name='course_type',type='integer',required=true,description="课程类型 1 团课 2 私教 3 活动")
     * @ApiParams(name='page',type='integer',required=true,description="分页码")
     * @ApiParams(name='province_id',type='string',required=false,description="省id")
     * @ApiParams(name='city_id',type='string',required=false,description="城市id")
     * @ApiParams(name='area_id',type='string',required=false,description="区id")
     * @ApiParams(name='cate_pid',type='string',required=false,description="课程一级分类")
     * @ApiParams(name='cate_id',type='string',required=false,description="课程二级分类")
     * @ApiParams(name='choose_date',type='string',required=false,description="选择的日期")
     * @ApiParams(name='start_at',type='string',required=false,description="开始时间")
     * @ApiParams(name='end_at',type='string',required=false,description="结束时间")
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function index(){
        $lat = $this->request->param('lat/s',$this->lat);
        $lng = $this->request->param('lng/s',$this->lng);
        $course_type = $this->request->param('course_type/d',1);
        $province_id = $this->request->param('province_id/s',0,'xilufitness_get_id_value');
        $city_id = $this->request->param('city_id/s',0,'xilufitness_get_id_value');
        $area_id = $this->request->param('area_id/s',0,'xilufitness_get_id_value');
        $cate_pid = $this->request->param('cate_pid/s',0,'xilufitness_get_id_value');
        $cate_id = $this->request->param('cate_id/s',0,'xilufitness_get_id_value');
        $choose_date = $this->request->param('choose_date/s');
        $start_at = $this->request->param('start_at/s');
        $end_at = $this->request->param('end_at/s');
        $choose_date = empty($choose_date) ? date('Y-m-d',time()) : $choose_date;
        $bannerList = (new \addons\xilufitness\model\Banner)->getBannerList($this->brand_id,2);
        $result = CourseService::getInstance()->getCourseList($lat,$lng,$course_type,$province_id,$city_id,$area_id,$cate_pid,$cate_id,$choose_date,$start_at,$end_at);
        $classTimeList = CourseService::getInstance()->getClassTime();
        $timeList = CourseService::getInstance()->getTimeList();
        $this->success('',array_merge(['bannerList' => $bannerList, 'classTimeList' => $classTimeList['list'], 'timeList' => $timeList, 'day_date' => date('Y-m-d',time())],$result));
    }

    /**
     * @ApiTitle('获取收藏的教练')
     * @ApiSummary('获取收藏的教练')
     * @ApiRoute('addons/xilufitness/course/getCollectCoach')
     * @ApiMethod('GET')
     * @ApiParams(name='lat',type='string',required=true,description="纬度")
     * @ApiParams(name='lng',type='string',required=true,description="经度")
     * @ApiParams(name='province_id',type='string',required=false,description="省id")
     * @ApiParams(name='city_id',type='string',required=false,description="城市id")
     * @ApiParams(name='area_id',type='string',required=false,description="区id")
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function getCollectCoach(){
        $lat = $this->request->param('lat',$this->lat);
        $lng = $this->request->param('lng',$this->lng);
        $province_id = $this->request->param('province_id/s',0,'xilufitness_get_id_value');
        $city_id = $this->request->param('city_id/s',0,'xilufitness_get_id_value');
        $area_id = $this->request->param('area_id/s',0,'xilufitness_get_id_value');
        $result = CourseService::getInstance()->getCollectCoach($lat,$lng,$province_id,$city_id,$area_id);
        $this->success('',$result);
    }


    /**
     * @ApiTitle('课程详情')
     * @ApiSummary('课程详情数据获取')
     * @ApiRoute('addons/xilufitness/course/detail')
     * @ApiMethod('GET')
     * @ApiParams(name='id',type='string',required=true,description="课程id")
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function detail(){
         $id = $this->request->param('id',0,'xilufitness_get_id_value');
         $result = CourseService::getInstance()->getDetail($id);
         $this->success('',$result);
    }

    /**
     * @ApiTitle('活动详情')
     * @ApiSummary('活动详情数据获取')
     * @ApiRoute('addons/xilufitness/course/getCampDetail')
     * @ApiMethod('GET')
     * @ApiParams(name='id',type='string',required=true,description="活动id")
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function getCampDetail(){
        $id = $this->request->param('id',0,'xilufitness_get_id_value');
        $result = CourseService::getInstance()->getCampDetail($id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle('获取课程评价')
     * @ApiSummary('获取课程评价')
     * @ApiRoute('addons/xilufitness/course/getCommentList')
     * @ApiMethod('GET')
     * @ApiParams(name='id',type='string',required=true,description="课程/活动/id")
     * @ApiParams(name='course_type',type='integer',required=true,description="类型 1 团课 2 私教 3 活动")
     * @ApiParams(name='page',type='integer',required=true,description="分页码")
     * @ApiParams(name='shop_id',type='string',required=false,description="门店id")
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function getCommentList(){
        $id = $this->request->param('id',0,'xilufitness_get_id_value');
        $shop_id = $this->request->param('shop_id',0,'xilufitness_get_id_value');
        $course_type = $this->request->param('course_type/d',0);
        $result = CourseService::getInstance()->getCommentList($id,$course_type,$shop_id);
        $this->success('',$result);
    }
}