<?php


namespace app\admin\controller\xilufitness\analyse;


use app\admin\controller\xilufitness\traits\Fitness;
use app\admin\model\Admin;
use app\admin\model\User;
use app\common\controller\Backend;
use app\common\model\Attachment;
use fast\Date;
use think\Db;
use think\Log;

class Amount extends Backend
{
    use Fitness;
    protected $noNeedRight = ['get_data'];

    /**
     * 数据统计查看
     */
    public function index()
    {
        $dayDate = $this->getDayDate();
        $start_at = $dayDate['start_at'];
        $end_at = $dayDate['end_at'];
        //1 团课 2 私教 3 活动 4 购买会员卡 5 购买会员卡套餐 0 充值
        $dayAnalyse['amount_00'] = $this->getOrderAmount(1,'0', $start_at, $end_at); //今日充值金额
        $dayAnalyse['amount_01'] = $this->getOrderAmount(1,'1', $start_at, $end_at); //今日团课报名金额
        $dayAnalyse['amount_02'] = $this->getOrderAmount(1,'2', $start_at, $end_at); //今日私教课报名金额
        $dayAnalyse['amount_03'] = $this->getOrderAmount(1,'3', $start_at, $end_at); //今日活动报名金额
        $dayAnalyse['amount_04'] = $this->getOrderAmount(1,'4', $start_at, $end_at); //今日会员卡购卡金额
        $dayAnalyse['amount_05'] = $this->getOrderAmount(1,'5', $start_at, $end_at); //今日会员卡套餐金额
        $dayAnalyse['total_amount'] = $this->getOrderAmount(2,null, $start_at, $end_at); //总金额
        Log::log($dayAnalyse);
        $this->assign('dayAnalyse',$dayAnalyse);
        $login_account_type = $this->getFitnessAccountRole(); //登录账号类型
        $this->assign('is_fitness_shop',$login_account_type == 2 ? 1 : 0);
        return $this->view->fetch();
    }


    /**
     *  获取订单金额
     */
    private function getOrderAmount(int $key, $order_type, $start_at, $end_at){
        $c_where = $this->getCommonWhere();
        $amount = 0;
        switch ($key){
            case $key == 1:
                Log::log("订单金额");
                $amount = Db::name('xilufitness_order')
                    ->where(['order_type' => $order_type, 'pay_status' => 1, 'pay_time' => [['egt',$start_at],['elt',$end_at]]])
                    ->where(function ($query) use($c_where){
                        if(!empty($c_where['brand_id'])){
                            $query->where('brand_id','eq',$c_where['brand_id']);
                        }
                        if(!empty($c_where['shop_id'])){
                            $query->where('shop_id','eq',$c_where['shop_id']);
                        }
                    })
                    ->sum('pay_amount');
                break;
            case $key == 2:
                Log::log("订单总金额");
                $amount = Db::name('xilufitness_order')
                    ->where(['order_type' => ['in', ['0','1','2','3','4','5'] ], 'pay_status' => 1, 'pay_time' => [['egt',$start_at],['elt',$end_at]]])
                    ->where(function ($query) use($c_where){
                        if(!empty($c_where['brand_id'])){
                            $query->where('brand_id','eq',$c_where['brand_id']);
                        }
                        if(!empty($c_where['shop_id'])){
                            $query->where('shop_id','eq',$c_where['shop_id']);
                        }
                    })
                    ->sum('pay_amount');
        }
        return $amount;
    }

    /**
     * 订单数据展示
     */
    public function get_data(){
        $c_where = $this->getCommonWhere();
        $datetime = $this->request->param('datetime','');
        $viewType = $this->request->param('viewType','day');
        $range_data = $this->dateRangeList($datetime,$viewType);
        $x_data = [];
        $y_data = [];
        $column = [];
        //订单数量统计
        $column[0] = '总金额';
        foreach ($range_data['list'] as $key => $val){
            if ($viewType == 'day') {
                $start_at = $key." 00:00:00";
                $end_at = $key." 23:59:59";
                $x_data[] = $val;
            }
            if ($viewType == 'week') {
                $start_at = $val['weekStart']." 00:00:00";
                $end_at = $val['weekEnd']." 23:59:59";
                $x_data[] = $key;
            }
            if ($viewType == 'month') {
                $start_at = $val['monthStart']." 00:00:00";
                $end_at = $val['monthEnd']." 23:59:59";
                $x_data[] = $key;
            }

            $y_data['data_0'][] = Db::name('xilufitness_order')
                ->where(function ($query) use($start_at,$end_at,$c_where){
                    $query->where(['order_type' => ['in', ['0','1','2','3','4','5'] ], 'pay_status' => 1, 'pay_time' => [['egt',strtotime($start_at)],['elt',strtotime($end_at)]] ]);
                    if(!empty($c_where['brand_id'])){
                        $query->where('brand_id',$c_where['brand_id']);
                    }
                    if(!empty($c_where['shop_id'])){
                        $query->where('shop_id',$c_where['shop_id']);
                    }
                })
                ->sum('pay_amount');
            unset($start_at);
            unset($end_at);
        }
        $result = ['series' => $y_data, 'fieldtextdata' => $column,'column' =>$x_data];
        return json(['code' => 1, 'data' => $result]);
    }

    /**
     * 根据日期范围返回x轴日期数据列表
     */
    private function dateRangeList($datetime,$viewType): array
    {
        $list = [];
        if(empty($datetime)){
            // 获取当月第一天
            $firstDayOfMonth = strtotime('first day of this month');
            // 获取当月最后一天
            $lastDayOfMonth = strtotime('last day of this month');
            $datetime = date('Y-m-d H:i:s',$firstDayOfMonth).' - ' . date('Y-m-d H:i:s',$lastDayOfMonth);
        }
        Log::log($datetime);
        $date_range = explode(" - ",$datetime);
        $current_time = strtotime(date('Y-m-d',strtotime($date_range[0])));
        $end_time = strtotime(date('Y-m-d',strtotime($date_range[1])));
        if ($viewType == 'day') {
            $i = 0;
            do{
                $next_time = date('Y-m-d',strtotime("+$i day",$current_time));
                $list[$next_time] = $next_time;
            } while($end_time > strtotime($next_time) && ++$i );
        }

        if ($viewType == 'week') {
            $i = 1;
            $next_time = $current_time;
            do{
                //获取当前周的第一天和最后一天
                $weekStart = strtotime("Monday this week", $next_time);
                $weekEnd = strtotime("Sunday this week", $next_time);

                $weekItem['weekStart'] = date('Y-m-d',$weekStart);
                $weekItem['weekEnd'] = date('Y-m-d',$weekEnd);

                //格式化
                $weekStartStr= date('m/d',$weekStart);
                $weekEndStr= date('m/d',$weekEnd);

                $key = "第" . $i . "周\n(" . $weekStartStr . "~" . $weekEndStr . ")";
                $list[$key]=$weekItem;
                if ($end_time <= $weekEnd) {
                    break;
                }else{
                    $next_time = strtotime("+7 day",$weekStart);
                }
                ++$i;
            } while(true);
        }

        if ($viewType == 'month') {
            $i = 1;
            $next_time = $current_time;
            do{
                //获取当前周的第一天和最后一天
                $monthStart = strtotime("first day of this month", $next_time);
                $monthEnd = strtotime("last day of this month", $next_time);

                $monthItem['monthStart'] = date('Y-m-d',$monthStart);
                $monthItem['monthEnd'] = date('Y-m-d',$monthEnd);

                //格式化
                $key = date('Y-m',$monthStart) . "月";
                $list[$key] = $monthItem;
                if ($end_time <= $monthEnd) {
                    break;
                }else{
                    $next_time = strtotime("+1 month",$monthStart);
                }
                ++$i;
            } while(true);
        }
        return ['list' => $list];
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