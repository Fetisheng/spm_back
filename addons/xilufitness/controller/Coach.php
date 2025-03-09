<?php


namespace addons\xilufitness\controller;

use addons\xilufitness\services\CoachService;
use addons\xilufitness\services\CourseService;

/**
 * @ApiSector(教练接口)
 * @ApiRoute(addons/xilufitness/coach)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Coach extends Base
{
   protected $noNeedLogin = ['index','detail','getList'];
    /**
     * @ApiTitle('教练列表')
     * @ApiSummary('获取教练列表')
     * @ApiRoute('addons/xilufitness/coach/index')
     * @ApiMethod('GET')
     * @ApiParams(name='lat',type='string',required=true,description="纬度")
     * @ApiParams(name='lng',type='string',required=true,description="经度")
     * @ApiParams(name='province_id',type='string',required=false,description='省id')
     * @ApiParams(name='city_id',type='string',required=false,description='市id')
     * @ApiParams(name='area_id',type='string',required=false,description='区域id')
     * @ApiParams(name='cate_pid',type='string',required=false,description='顶级课程分类id')
     * @ApiParams(name='cate_id',type='string',required=false,description='子级课程分类id')
     * @ApiParams(name='keywords',type='string',required=false,description='关键词')
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
        $province_id = $this->request->param('province_id/s',0,'xilufitness_get_id_value');
        $city_id = $this->request->param('city_id/s',0,'xilufitness_get_id_value');
        $area_id = $this->request->param('area_id/s',0,'xilufitness_get_id_value');
        $cate_pid = $this->request->param('cate_pid/s',0,'xilufitness_get_id_value');
        $cate_id = $this->request->param('cate_id/s',0,'xilufitness_get_id_value');
        $keywords = $this->request->param('keywords/s','');
        $result = CoachService::getInstance()->getCoachList($lat,$lng,$cate_pid,$cate_id,$keywords,$city_id,$province_id,$area_id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle('获取教练详情')
     * @ApiSummary('获取教练详情')
     * @ApiRoute('addons/xilufitness/coach/detail')
     * @ApiMethod('GET')
     * @ApiParams(name='id',type='string',required=true,description="教练id")
     * @ApiParams(name='shop_id',type='string',required=true,description="门店id")
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
        $shop_id = $this->request->param('shop_id',0,'xilufitness_get_id_value');
        $result = CoachService::getInstance()->getDetail($id,$shop_id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle('获取教练/课程/活动/列表')
     * @ApiSummary('获取教练/课程/活动/列表')
     * @ApiRoute('addons/xilufitness/coach/getList')
     * @ApiMethod('GET')
     * @ApiParams(name='id',type='string',required=true,description="教练id")
     * @ApiParams(name='shop_id',type='string',required=true,description="门店id")
     * @ApiParams(name='is_type',type='integer',required=true,description="类型 1 团课 2 私教 3 活动")
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
    public function getList(){
        $id = $this->request->param('id',0,'xilufitness_get_id_value');
        $shop_id = $this->request->param('shop_id',0,'xilufitness_get_id_value');
        $is_type = $this->request->param('is_type/d',0);
        $result = CoachService::getInstance()->getList($id,$shop_id,$is_type);
        $this->success('',$result);
    }

    /**
     * @ApiTitle('获取教练个人数据信息')
     * @ApiSummary('获取教练个人数据信息')
     * @ApiRoute('addons/xilufitness/coach/getCoachInfo')
     * @ApiMethod('GET')
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
    public function getCoachInfo(){
       $result = CoachService::getInstance()->getCoachInfo();
       $this->success('',$result);
    }

    /**
     * @ApiTitle('提交报备信息')
     * @ApiSummary('提交报备信息')
     * @ApiRoute('addons/xilufitness/coach/addReport')
     * @ApiMethod('post')
     * @ApiParams(name='start_at',type='string',require = true, description = '开始时间')
     * @ApiParams(name='end_at',type='string',require = true, description = '结束时间')
     * @ApiParams(name='description',type='string',require = true, description = '请假事由')
     * @ApiParams(name='id',type='string',require = true, description = '教练id')
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
    public function addReport(){
        $start_at = $this->request->param('start_at');
        $end_at = $this->request->param('end_at');
        $description = $this->request->param('description');
        $id = $this->request->param('id',0,'xilufitness_get_id_value');
        $result = CoachService::getInstance()->addReport($id,$start_at,$end_at,$description);
        if($result['code'] == 1){
            $this->success('请求成功');
        }
        $this->error($result['msg'] ?? '提交失败');
    }

    /**
     * @ApiTitle('报备信息列表')
     * @ApiSummary('报备信息列表')
     * @ApiRoute('addons/xilufitness/coach/getReports')
     * @ApiMethod('post')
     * @ApiParams(name='page',type='integer',require = true, description = '分页码')
     * @ApiParams(name='id',type='string',require = true, description = '教练id')
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
    public function getReports(){
        $id = $this->request->param('id',0,'xilufitness_get_id_value');
        $result = CoachService::getInstance()->getReportList($id);
        $this->success('',$result);
    }

    /**
     * @ApiTitle('收入明细列表')
     * @ApiSummary('收入明细列表')
     * @ApiRoute('addons/xilufitness/coach/getCashList')
     * @ApiMethod('post')
     * @ApiParams(name='page',type='integer',require = true, description = '分页码')
     * @ApiParams(name='id',type='string',require = true, description = '教练id')
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
    public function getCashList(){
        $id = $this->request->param('id',0,'xilufitness_get_id_value');
        $result = CoachService::getInstance()->getCashList($id);
        $this->success('',$result);
    }
    /**
     * @ApiTitle('教练提现')
     * @ApiSummary('教练提现')
     * @ApiRoute('addons/xilufitness/coach/addWithdraw')
     * @ApiMethod('post')
     * @ApiParams(name='withdraw_price',type='Floater',require = true, description = '提现金额')
     * @ApiParams(name='id',type='string',require = true, description = '教练id')
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
    public function addWithdraw(){
        $id = $this->request->param('id',0,'xilufitness_get_id_value');
        $price = $this->request->param('withdraw_price');
        $result = CoachService::getInstance()->addWithdraw($id,$price);
        if($result['code'] == 1){
            $this->success('提交成功');
        } else {
            $this->error($result['msg'] ?? '提交失败');
        }
    }

    /**
     * @ApiTitle('教练排课列表')
     * @ApiSummary('教练排课列表')
     * @ApiRoute('addons/xilufitness/coach/getScheduling')
     * @ApiMethod('get')
     * @ApiParams(name='course_type',type='integer',require = true, description = '课程类型')
     * @ApiParams(name='day_date',type='string',require = true, description = '日期')
     * @ApiParams(name='page',type='integer',require = true, description = '分页码')
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
    public function getScheduling(){
        $course_type = $this->request->param('course_type/d',1);
        $day_date = $this->request->param('day_date','');
        $day_date = !empty($day_date) && !is_numeric($day_date) ? strtotime($day_date) : strtotime(date('Y-m-d',time()));
        $result = CoachService::getInstance()->getScheduling($course_type,$day_date);
        $timeList = CourseService::getInstance()->getClassTime();
        $result = array_merge($result,['timeList' => $timeList['list'], 'day_date' => date('Y-m-d',time())]);
        $this->success('',$result);
    }

    /**
     * @ApiTitle('教练排课详情')
     * @ApiSummary('教练排课详情')
     * @ApiRoute('addons/xilufitness/coach/getSchedulingDetail')
     * @ApiMethod('get')
     * @ApiParams(name='is_type',type='integer',require = true, description = '类型')
     * @ApiParams(name='id',type='string',require = true, description = '排课id')
     * @ApiParams(name='work_camp_id',type='string',require = true, description = '活动排课id')
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
    public function getSchedulingDetail(){
        $id = $this->request->param('id',0,'xilufitness_get_id_value');
        $work_camp_id = $this->request->param('work_camp_id',0,'xilufitness_get_id_value');
        $is_type = $this->request->param('is_type/d',1);
        $result = CoachService::getInstance()->getSchedulingDetail($id,$work_camp_id,$is_type);
        $userListResult = CourseService::getInstance()->getSignList(($is_type == 3 ? $work_camp_id : $id),$is_type);
        $result = array_merge($result,['userList' => $userListResult['list'], 'user_count' => $userListResult['user_count']]);
        $this->success('',$result);
    }

    /**
     * @ApiTitle('学员排行')
     * @ApiSummary('教练排课详情')
     * @ApiRoute('addons/xilufitness/coach/getStudentRanking')
     * @ApiMethod('get')
     * @ApiParams(name='page',type='integer',require = true, description = '分页')
     * @ApiParams(name='coach_id',type='string',require = true, description = '教练id')
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
    public function getStudentRanking(){
        $coach_id = $this->request->param('coach_id',0,'xilufitness_get_id_value');
        $result = CoachService::getInstance()->getStudentRanking($coach_id);
        $this->success('',$result);
    }




}