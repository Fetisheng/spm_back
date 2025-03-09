<?php


namespace app\admin\controller\xilufitness\analyse;


use app\admin\controller\xilufitness\traits\Fitness;
use app\admin\model\Admin;
use app\admin\model\User;
use app\common\controller\Backend;
use app\common\model\Attachment;
use fast\Date;
use think\Db;

class Index extends Backend
{
    use Fitness;

    /**
     * 数据统计查看
     */
    public function index()
    {
        $dayDate = $this->getDayDate();
        $courseAnalyse['course_1'] = Db::name('xilufitness_work_course')->where($this->getWhereParams(1,1))->count('*'); //待开团课
        $courseAnalyse['course_2'] = Db::name('xilufitness_work_course')->where($this->getWhereParams(2,2))->count('*'); //待开私教课
        $courseAnalyse['course_3'] = Db::name('xilufitness_work_camp')->where($this->getWhereParams(3,3))->count('*'); //待开活动
        $courseAnalyse['coach_count'] = Db::name('xilufitness_coach')->where($this->getWhereParams(4,0))->count('*'); //教练
        $dayAnalyse['recharge_amount'] = Db::name('xilufitness_order')
            ->where($this->getWhereParams(5,0,$dayDate['start_at'],$dayDate['end_at']))
            ->sum('pay_amount'); //今日充值金额
        $dayAnalyse['record_count'] = Db::name('xilufitness_order_verification_records')
            ->where($this->getWhereParams(6,0,$dayDate['start_at'],$dayDate['end_at']))
            ->count('*'); //今日核销课程数
        $dayAnalyse['sign_count'] = Db::name('xilufitness_order')
            ->where($this->getWhereParams(7,0,$dayDate['start_at'],$dayDate['end_at']))
            ->count('*');//今日报名数
        $dayAnalyse['user_count'] = Db::name('xilufitness_user')
            ->where($this->getWhereParams(8,0,$dayDate['start_at'],$dayDate['end_at']))
            ->count('*');//今日会员注册数

        $analyseData['shop_count'] = Db::name('xilufitness_shop')
            ->where($this->getWhereParams(9,0))
            ->count('*'); //门店数量
        $analyseData['user_count'] = Db::name('xilufitness_user')
            ->where($this->getWhereParams(10,0))
            ->count('*'); //会员总数量
        $analyseData['recharge_total_amount'] = Db::name('xilufitness_order')
            ->where($this->getWhereParams(11,0))
            ->sum('pay_amount'); //充值总金额
        $analyseData['sign_total_amount'] = Db::name('xilufitness_order')
            ->where($this->getWhereParams(12,0))
            ->sum('pay_amount'); //报名总金额
        $this->assign('courseAnalyse',$courseAnalyse);
        $this->assign('dayAnalyse',$dayAnalyse);
        $this->assign('analyseData',$analyseData);
        $login_account_type = $this->getFitnessAccountRole(); //登录账号类型
        $this->assign('is_fitness_shop',$login_account_type == 2 ? 1 : 0);
        return $this->view->fetch();
    }

    /**
     * 订单数据展示
     */
    public function get_data(){
        $c_where = $this->getCommonWhere();
        $datetime = $this->request->param('datetime','');
        $range_data = $this->dateRange($datetime);
        $x_data = [];
        $y_data = [];
        $column = [];
        //订单数量统计
        $column[0] = '团课';
        $column[1] = '私教课';
        $column[2] = '活动';

        foreach ($range_data['list'] as $key => $val){
            $start_at = $range_data['range_type'] == 1 ? $key.":00:00" : $key." 00:00:00";
            $end_at = $range_data['range_type'] == 1 ? $key.":59:50" : $key." 23:59:59";
            $x_data[] = $val;
            $y_data['data_0'][] = Db::name('xilufitness_order')
                ->where(function ($query) use($start_at,$end_at,$c_where){
                    $query->where(['order_type' => 1, 'pay_status' => 1, 'pay_time' => [['egt',strtotime($start_at)],['elt',strtotime($end_at)]] ]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('shop_id',$c_where['shop_id']);
                    }
                })
                ->count('*');
            $y_data['data_1'][] = Db::name('xilufitness_order')
                ->where(function ($query) use($start_at,$end_at,$c_where){
                    $query->where(['order_type' => 2, 'pay_status' => 1, 'pay_time' => [['egt',strtotime($start_at)],['elt',strtotime($end_at)]] ]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('shop_id',$c_where['shop_id']);
                    }
                })
                ->count('*');
            $y_data['data_3'][] = Db::name('xilufitness_order')
                ->where(function ($query) use($start_at,$end_at,$c_where){
                    $query->where(['order_type' => 3, 'pay_status' => 1, 'pay_time' => [['egt',strtotime($start_at)],['elt',strtotime($end_at)]] ]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('shop_id',$c_where['shop_id']);
                    }
                })
                ->count('*');
            unset($start_at);
            unset($end_at);
        }
        $result = ['series' => $y_data, 'fieldtextdata' => $column,'column' =>$x_data];
        return json(['code' => 1, 'data' => $result]);
    }

    /**
     * 根据日期范围返回x轴日期数据列表
     */
    private function dateRange($datetime=''){
        $list = [];
        if(empty($datetime)){
            //默认近 8天的数据
            $current_time = strtotime(date('Y-m-d',strtotime("-7 day",time())));
            $end_time = strtotime(date('Y-m-d',time()));
            $i = 0;
            do{
                $next_time = date('Y-m-d',strtotime("+$i day",$current_time));
                $list[$next_time] = $next_time;
            } while($end_time > strtotime($next_time) && ++$i );
            $range_type = 2;
        } else {
            $date_range = explode(" - ",$datetime);
            $total_time = strtotime($date_range[1]) - strtotime($date_range[0]);
            if($total_time <= 86400){
                //一天内的
                $day_time = date('Y-m-d',strtotime($date_range[0]));
                $day_times = ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
                foreach ($day_times as $key => $val){
                    $list[$day_time.' '.$val] = $day_time.' '.$val.'点';
                }
                $range_type = 1;
            } else {
                $current_time = strtotime($date_range[0]);
                $end_time = strtotime(date('Y-m-d',strtotime($date_range[1])));
                $i = 0;
                do{
                    $next_time = date('Y-m-d',strtotime("+$i day",$current_time));
                    $list[$next_time] = $next_time;
                } while($end_time > strtotime($next_time) && ++$i );
                $range_type = 2;
            }
        }
        return ['list' => $list, 'range_type' => $range_type];
    }

    /**
     *  获取搜索条件
     */
    private function getWhereParams(int $key,int $course_type=0,$start_at='',$end_at=''){
        $c_where = $this->getCommonWhere();
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
    //添加
    public function add(){
        return;
    }
    //编辑
    public function edit($ids = null) {
        return;
    }
    //删除
    public function del($ids = null) {
        return;
    }
    //回收站列表
    public function recyclebin() {
        return;
    }
    //回收站(真实删除或清空)
    public function destroy($ids = null) {
        return;
    }
    //回收站还原
    public function restore($ids = null) {
        return;
    }
    //批量操作(修改状态)
    public function multi($ids = null) {
        return;
    }



}