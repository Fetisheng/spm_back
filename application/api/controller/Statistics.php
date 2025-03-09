<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\exception\DbException;
use think\Log;

/**
 * 财务统计
 */
class Statistics extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    /**
     * 财务统计
     *
     * @ApiTitle    (财务统计)
     * @ApiSummary  (财务统计)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/statistics/index)
     * @ApiParams   (name="brand_id", type="integer", required=true, description="商户ID")
     * @ApiParams   (name="shop_id", type="integer", required=true, description="门店ID")
     * @ApiReturn   ({
        'code':'1',
        'msg':'返回成功'
        })
     */
    public function index()
    {
        $common_where['brand_id'] = $this->request->post("brand_id");
        $common_where['shop_id'] = $this->request->post("shop_id");
        $dayDate = $this->getDayDate();
        $courseAnalyse['course_1']['title'] = "待开团课";
        $courseAnalyse['course_1']['value'] = Db::name('xilufitness_work_course')->where($this->getWhereParams($common_where,1,1))->count('*'); //待开团课
        $courseAnalyse['course_2']['title'] = "待开私教课";
        $courseAnalyse['course_2']['value'] = Db::name('xilufitness_work_course')->where($this->getWhereParams($common_where,2,2))->count('*'); //待开私教课
        $courseAnalyse['course_3']['title'] = "待开活动";
        $courseAnalyse['course_3']['value'] = Db::name('xilufitness_work_camp')->where($this->getWhereParams($common_where,3,3))->count('*'); //待开活动
        $courseAnalyse['coach_count']['title'] = "教练数量";
        $courseAnalyse['coach_count']['value'] = Db::name('xilufitness_coach')->where($this->getWhereParams($common_where,4,0))->count('*'); //教练
        $dayAnalyse['recharge_amount']['title'] = "今日充值金额";
        $dayAnalyse['recharge_amount']['value'] = Db::name('xilufitness_order')
            ->where($this->getWhereParams($common_where,5,0,$dayDate['start_at'],$dayDate['end_at']))
            ->sum('pay_amount'); //今日充值金额
        $dayAnalyse['record_count']['title'] = "今日核销课程数";
        $dayAnalyse['record_count']['value'] = Db::name('xilufitness_order_verification_records')
            ->where($this->getWhereParams($common_where,6,0,$dayDate['start_at'],$dayDate['end_at']))
            ->count('*'); //今日核销课程数
        $dayAnalyse['sign_count']['title'] = "今日报名数";
        $dayAnalyse['sign_count']['value'] = Db::name('xilufitness_order')
            ->where($this->getWhereParams($common_where,7,0,$dayDate['start_at'],$dayDate['end_at']))
            ->count('*');//今日报名数
        $dayAnalyse['user_count']['title'] = "今日会员注册数";
        $dayAnalyse['user_count']['value'] = Db::name('xilufitness_user')
            ->where($this->getWhereParams($common_where,8,0,$dayDate['start_at'],$dayDate['end_at']))
            ->count('*');//今日会员注册数
        $analyseData['shop_count']['title'] = "门店数量";
        $analyseData['shop_count']['value'] = Db::name('xilufitness_shop')
            ->where($this->getWhereParams($common_where,9,0))
            ->count('*'); //门店数量
        $analyseData['user_count']['title'] = "会员总数量";
        $analyseData['user_count']['value'] = Db::name('xilufitness_user')
            ->where($this->getWhereParams($common_where,10,0))
            ->count('*'); //会员总数量
        $analyseData['recharge_total_amount']['title'] = "充值总金额";
        $analyseData['recharge_total_amount']['value'] = Db::name('xilufitness_order')
            ->where($this->getWhereParams($common_where,11,0))
            ->sum('pay_amount'); //充值总金额
        $analyseData['sign_total_amount']['title'] = "报名总金额";
        $analyseData['sign_total_amount']['value'] = Db::name('xilufitness_order')
            ->where($this->getWhereParams($common_where,12,0))
            ->sum('pay_amount'); //报名总金额
        //会员卡信息 购卡订单数 总金额 次卡数量 时长卡数量 今日售卡总数 今日售卡金额 核销总次数 今日核销次数
        if (!empty($common_where['brand_id'])) {
            $where_1['brand_id'] = $common_where['brand_id'];
        }
        $where_1['pay_status'] = 1;
        $where_1['order_type'] = 4;
        //购卡订单数
        $cardAnalyse['card_order_count']['title'] = "购卡订单数";
        $cardAnalyse['card_order_count']['value'] = Db::name('xilufitness_order')->where($where_1)->count('*');
        //购卡总金额
        $cardAnalyse['card_order_amount']['title'] = "购卡总金额";
        $cardAnalyse['card_order_amount']['value'] = Db::name('xilufitness_order')->where($where_1)->sum('pay_amount');
        //今日售卡总数
        $where_1['pay_time'] = [['egt', $dayDate['start_at']], ['elt', $dayDate['end_at']]];
        $cardAnalyse['card_order_day_count']['title'] = "今日售卡总数";
        $cardAnalyse['card_order_day_count']['value'] = Db::name('xilufitness_order')->where($where_1)->count('*');
        //今日售卡金额
        $cardAnalyse['card_order_day_amount']['title'] = "今日售卡金额";
        $cardAnalyse['card_order_day_amount']['value'] = Db::name('xilufitness_order')->where($where_1)->sum('pay_amount');

        //核销总次数
        if (!empty($common_where['brand_id'])) {
            $where_2['brand_id'] = $common_where['brand_id'];
        }
        $where_2['status'] = 1;
        $cardAnalyse['check_count']['title'] = "核销总次数";
        $cardAnalyse['check_count']['value'] = Db::name('xilufitness_card_verification_records')->where($where_2)->count('*');

        $where_2['check_time'] = [['egt', $dayDate['start_at']], ['elt', $dayDate['end_at']]];
        $cardAnalyse['check_day_count']['title'] = "今日核销次数";
        $cardAnalyse['check_day_count']['value'] = Db::name('xilufitness_card_verification_records')->where($where_2)->count('*');

        //次卡数量
        if (!empty($common_where['brand_id'])) {
            $where_3['brand_id'] = $common_where['brand_id'];
        }
        $where_3['card_type'] = 1;
        $where_3['status'] = 1;
        $cardAnalyse['times_card_count']['title'] = "次卡数量";
        $cardAnalyse['times_card_count']['value'] = Db::name('xilufitness_user_card')->where($where_3)->count('*');
        //时长卡数量
        $where_3['card_type'] = 2;
        $cardAnalyse['term_card_count']['title'] = "时长卡数量";
        $cardAnalyse['term_card_count']['value'] = Db::name('xilufitness_user_card')->where($where_3)->count('*');

        $info['courseAnalyse'] = $courseAnalyse;
        $info['dayAnalyse'] = $dayAnalyse;
        $info['analyseData'] = $analyseData;
        $info['cardAnalyse'] = $cardAnalyse;
        $this->success('查询成功',$info);
    }

    /**
     * 获取公共搜索条件信息
     */
    private function getCommonWhere(){
        $fitness_brand_id = $this->getFitnessBrandId();
        $fitness_shop_id = $this->getFitnessShopId();
        return ['brand_id' => $fitness_brand_id, 'shop_id' => $fitness_shop_id];
    }

    /**
     * 获取今日时间
     */
    private function getDayDate(){
        $start_at = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $end_at = mktime(23,59,59,date('m'),date('d'),date('Y'));
        return ['start_at' => $start_at, 'end_at' => $end_at];
    }

    /**
     *  获取搜索条件
     */
    private function getWhereParams($common_where,int $key,int $course_type=0,$start_at='',$end_at=''){
        $c_where = $common_where;
        switch ($key){
            case $key == 1:
            case $key == 2:
                $where = function ($query) use($c_where,$course_type){
                    $query->where(['status' => 'normal', 'course_type' => $course_type, 'start_at' => ['gt',time()] ]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('shop_id','eq',$c_where['shop_id']);
                    }
                };
                break;
            case $key == 3:
                $where = function ($query) use($c_where,$course_type){
                    $query->where(['status' => 'normal','start_at' => ['gt',time()] ]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('shop_id','eq',$c_where['shop_id']);
                    }
                };
                break;
            case $key == 4:
                $where = function ($query) use($c_where){
                    $query->where(['status' => 'normal']);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->whereRaw("FIND_IN_SET({$c_where['shop_id']},`shop_ids`)");
                    }
                };
                break;
            case $key == 5:
                $where = function ($query) use($c_where,$start_at,$end_at){
                    $query->where(['order_type' => 0, 'pay_status' => 1, 'pay_time' => [['egt',$start_at],['elt',$end_at]]]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('shop_id','eq',$c_where['shop_id']);
                    }
                };
                break;
            case $key == 6:
                $where = function ($query) use($c_where,$start_at,$end_at){
                    $query->where(['check_time' => [['egt',$start_at],['elt',$end_at]]]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('shop_id','eq',$c_where['shop_id']);
                    }
                };
                break;
            case $key == 7:
                $where = function ($query) use($c_where,$start_at,$end_at){
                    $query->where(['pay_time' => [['egt',$start_at],['elt',$end_at]], 'order_type' => [['neq',0],['neq',4]]]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('shop_id','eq',$c_where['shop_id']);
                    }
                };
                break;
            case $key == 8:
                $where = function ($query) use($c_where,$start_at,$end_at){
                    $query->where(['createtime' => [['egt',$start_at],['elt',$end_at]]]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                };
                break;
            case $key == 9:
                $where = function ($query) use($c_where){
                    $query->where(['status' => 'normal']);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('id','eq',$c_where['shop_id']);
                    }
                };
                break;
            case $key == 10:
                $where = function ($query) use($c_where){
                    $query->where(['status' => 'normal']);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                };
                break;
            case $key == 11:
                $where = function ($query) use($c_where){
                    $query->where(['pay_status' => 1, 'order_type' => 0]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                };
                break;
            case $key == 12:
                $where = function ($query) use($c_where){
                    $query->where(['pay_status' => 1, 'order_type' => [['neq',0],['neq',4]], 'order_status' => ['neq',4]]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id','eq',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('shop_id','eq',$c_where['shop_id']);
                    }
                };
        }
        return $where;
    }

    /**
     * 获取明细数据
     *
     * @ApiTitle    (获取明细数据)
     * @ApiSummary  (获取明细数据)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/statistics/get_detail_list)
     * @ApiParams   (name="brand_id", type="integer", required=true, description="商户ID")
     * @ApiParams   (name="shop_id", type="integer", required=false, description="门店ID (核销类型有效)")
     * @ApiParams   (name="sec_type", type="integer", required=false, description="统计类型 1：订单 2:核销")
     * @ApiParams   (name="page_index", type="integer", required=false, description="分页页码")
     * @ApiParams   (name="page_size", type="integer", required=false, description="每页条数")
     * @ApiReturn   ({
        'code':'1',
        'msg':'返回成功'
        })
     */
    public function get_detail_list()
    {
        $params['brand_id'] = $this->request->post("brand_id");
        $params['sec_date'] = $this->request->post("sec_date");
        $params['sec_type'] = $this->request->post("sec_type");
        $params['page_index'] = $this->request->post("page_index");
        $params['page_size'] = $this->request->post("page_size");
        Log::log($params);
        if ($params['sec_type'] == 1) {
            return $this->getOrderList($params);
        }
        if ($params['sec_type'] == 2) {
            $params['shop_id'] = $this->request->post("shop_id");
            return $this->getCheckList($params);
        }
        $this->success('返回成功', []);
    }


    /**
     * 获取订单明细数据
     *
     * @ApiTitle    (获取订单明细数据)
     * @ApiSummary  (获取订单明细数据)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/statistics/getOrderList)
     * @throws DbException
     */
    public function getOrderList($params)
    {
        $where['index.order_type'] = 4;
        if (!empty($params['brand_id'])) {
            $where['index.brand_id'] = $params['brand_id'];
        }
        if (!empty($params['sec_date'])) {

        }

        $page_index = empty($params['page_index']) ? 1 : $params['page_index'];

        $page_size = empty($params['page_size']) ? 10 : $params['page_size'];

        Log::log($where);
        $order_model = new \app\admin\model\xilufitness\order\Index();
        $list = $order_model
            ->field(['id','order_no','brand_id','user_id','data_id','trade_no','pay_amount','total_amount',
                'pay_status','order_status','pay_time','pay_type','deletetime','createtime'])
            ->with(['user' => function($query){
                return $query->withField(['nickname','status','mobile']);
            },'brand' => function($query){
                return $query->withField(['brand_name','status']);
            }, 'category' => function($query){
                $query->withField(['cardname','cardtypename']);
            }])
            ->where($where)
            ->order('index.createtime', 'desc')
            ->paginate($page_size, false, ['page' => $page_index]);
        return $list;
    }

    /**
     * 获取核销明细数据
     *
     * @ApiTitle    (获取核销明细数据)
     * @ApiSummary  (获取核销明细数据)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/statistics/getCheckList)
     * @throws DbException
     */
    public function getCheckList($params)
    {

        if (!empty($params['brand_id'])) {
            $where['card_verification_records.brand_id'] = $params['brand_id'];
        }
        if (!empty($params['shop_id'])) {
            $where['card_verification_records.shop_id'] = $params['shop_id'];
        }
        if (!empty($params['sec_date'])) {

        }

        $page_index = empty($params['page_index']) ? 1 : $params['page_index'];

        $page_size = empty($params['page_size']) ? 10 : $params['page_size'];

        $check_model = new \app\admin\model\xilufitness\card\CardVerificationRecords();
        $list= $check_model
            ->field(['id','user_card_id','share_id','shop_id','user_id','admin_user_id','check_time','check_type','status'])
            ->where($where)
            ->with(['user' => function($query){
                $query->withField(['nickname','avatar']);
            },'admin' => function($query){
                $query->withField(['nickname','avatar']);
            },'card' => function($query){
                $query->withField(['card_no', 'card_type']);
            },'shop' => function($query){
                return $query->withField(['shop_name','address']);
            },'brand' => function($query){
                $query->withField(['brand_name']);
            }])
            ->order('card_verification_records.check_time', 'desc')
            ->paginate($page_size, false, ['page' => $page_index]);
        foreach ($list as $k => $v) {
            if (empty($v->share_id)) {
                $card_info = $v['card'];

                if ($card_info -> card_type == 1) {
                    $v->card_type_name = '次卡';
                }
                if ($card_info -> card_type == 2) {
                    $v->card_type_name = '时长卡';
                }
            } else {
                $v->card_type_name = '单次卡';
            }
            if($v->status == 1){
                $v->status_name = '核销成功';
            }
            if($v->status == 0){
                $v->status_name = '核销失败';
            }
        }
        return $list;
    }

}