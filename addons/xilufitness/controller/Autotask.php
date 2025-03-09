<?php


namespace addons\xilufitness\controller;


use addons\xilufitness\model\TimesCardShare;
use addons\xilufitness\model\UserCard;
use addons\xilufitness\services\pay\PayService;
use think\Config;
use think\Db;
use think\Log;
/**
 * @ApiSector(定时任务)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Autotask extends \think\addons\Controller
{
    protected $noNeedLogin = ["*"];

    protected $layout = '';


    public function _initialize()
    {
        parent::_initialize();

        if (!$this->request->isCli()) {
            $this->error('只允许在终端进行操作!');
        }
    }

    /**
     * 获取 配置信息
     * @param int $brand_id 品牌商id
     * @return array
     */
    private function getConfig(int $brand_id = 0){
        $brandConfig = Config::get('xilubrand')[$brand_id] ?? '';
        return [
            'appid'          => $brandConfig['mini_appid'] ?? '',
            'appsecret'      => $brandConfig['mini_appsecret'] ?? '',
            'encodingaeskey' => '',
            // 配置商户支付参数
            'mch_id'         => $brandConfig['mini_mch_id'] ?? '',
            'mch_key'        => $brandConfig['mini_mch_key'] ?? '',
            // 配置商户支付双向证书目录 （p12 | key,cert 二选一，两者都配置时p12优先）
            'ssl_p12'        => ROOT_PATH.'public'.$brandConfig['mini_mch_p12'] ?? '',
            'ssl_key'       => ROOT_PATH.'public'.$brandConfig['mini_mch_pem_key'] ?? '',
            'ssl_cer'       => ROOT_PATH.'public'.$brandConfig['mini_mch_pem_cert'] ?? '',
            // 配置缓存目录，需要拥有写权限
            'cache_path'     => RUNTIME_PATH.'wechat',
        ];
    }

    /**
     * 计划任务入口
     */
    public function cron(){
        Log::log("[".date('Y-m-d H:i:s'."]"." - 开始执行定时任务"));
        $this->change_work_course();
        $this->change_work_camp();
        $this->cancel_wait();
        $msg = $this->change_invalid_share();
        Log::log("[".date('Y-m-d H:i:s'."]"." - 执行定时任务结束"));
        return $msg;
    }

    /**
     * 清理失效的次卡分享
     */
    public function change_invalid_share()
    {
        Log::log("[".date('Y-m-d H:i:s'."]"." - 开始清理失效的次卡分享"));
        $share_card = new TimesCardShare();
        $user_card = new UserCard();
        //查询失效的次卡分享
        $share_list = $share_card
            ->where('status',0)
            ->where('share_expire_time', '<', date('Y-m-d H:i:s'))
            ->select();
        foreach ($share_list as $k => $v) {
            try {
                Db::startTrans();
                $share_id = xilufitness_get_id_value($v->id);
                Log::log($share_id);
                $data['status'] = 2;
                $result = Db::name('xilufitness_times_card_share')->where('id', $share_id)->update($data);
                Log::log($result);
                // 判断更新是否成功
                if ($result) {
                    $user_card_id = $v->user_card_id;
                    $card_info = $user_card::get($user_card_id);
                    $card_info['left_times_count']  = $card_info['left_times_count'] + 1;
                    $card_info['already_share_times']  = $card_info['already_share_times'] - 1;
                    $card_info['left_share_times']  = $card_info['left_share_times'] + 1;
                    $card_info->save();
                    Log::log("清理失效的次卡分享成功");
                }
                Db::commit();
            } catch (\Exception $e) {
                Log::log("清理失效的次卡分享失败");
                Log::log($e);
                Db::rollback();
            }
        }
        Log::log( "清理失效的次卡分享" . count($share_list) . "条");
        Log::log("[".date('Y-m-d H:i:s'."]"." - 清理失效的次卡分享结束"));
        return "清理失效的次卡分享" . count($share_list) . "条";
    }


    /**
     * 开课前五分钟 排队取消 排队失效
     */
    public function cancel_wait(){
        $model = new \addons\xilufitness\model\Order;
        $time = time() - 300;
        $i = 1;
        do {
            try {
                $total_count = $model->where(['order_status' => 10, 'starttime' => ['egt',$time]])->count('*');
                $list = $model
                    ->where(['order_status' => 10, 'starttime' => ['egt',$time]])
                    ->order("pay_time asc")
                    ->page($i,100)
                    ->select();
                $current_count = ($i-1)*100 + count($list);
                foreach ($list as $key => $val){
                    Db::startTrans();
                    $cancelResult = $val->save(['order_status' => 4]);
                    if(false !== $cancelResult){
                        PayService::getInstance(['mini_config' => $this->getConfig($val['brand_id'])])->refundOrder(xilufitness_get_id_value($val['id']),($val['brand_id'] ?? 0));
                    }
                    Db::commit();
                    unset($cancelResult);
                }
            } catch (\Exception $e){
                Db::rollback();
                Log::record("取消排队失败:".$e->getMessage());
            }
        } while($total_count > $current_count && ++$i);
    }

    /**
     * 检测课程排课到了开课时间，状态还在待预约 只要有人报名改为 开课成功
     */

    public function change_work_course(){
        $model = new \addons\xilufitness\model\WorkCourse;
        $orderModel = new \addons\xilufitness\model\Order;
        $i = 1;
        $page_size = 100;
        do {
            $total_count = $model->where(['status' => 'normal', 'start_at' => ['elt',time()]])->count('*');
            $list = $model
                ->where(['status' => 'normal', 'start_at' => ['elt',time()]])
                ->order("start_at asc")
                ->page($i,$page_size)
                ->select();
            array_walk($list,function (&$item,$key) use($orderModel){
                $order_count = $orderModel->where(['brand_id' => $item->brand_id ?? 0, 'data_id' => xilufitness_get_id_value($item->id ?? 0), 'order_type' => $item->course_type ?? -1,
                    'order_status' => ['notin',[0,4,10]]])->count('*');
                if($order_count > 0){
                    $item->allowField(true)->save(['status' => 'complete']);
                } else {
                    $item->allowField(true)->save(['status' => 'failed']);
                }
            });
            $current_count = ($i - 1)*$page_size + count($list);

        } while($total_count > $current_count && ++$i);
    }

    /**
     * 检测活动排课到期情况
     */
    public function change_work_camp(){
        $model = new \addons\xilufitness\model\WorkCamp;
        $orderModel = new \addons\xilufitness\model\Order;
        $i = 1;
        $page_size = 100;
        do {
            $total_count = $model->where(['status' => 'normal', 'start_at' => ['elt',time()]])->count('*');
            $list = $model
                ->where(['status' => 'normal', 'start_at' => ['elt',time()]])
                ->order("start_at asc")
                ->page($i,$page_size)
                ->select();
            array_walk($list,function (&$item,$key) use($orderModel){
                $order_count = $orderModel->where(['brand_id' => $item->brand_id ?? 0, 'data_id' => xilufitness_get_id_value($item->id ?? 0), 'order_type' => 3,
                    'order_status' => ['notin',[0,4]]])->count('*');
                if($order_count >= $item['camp_count']){
                    $item->allowField(true)->save(['status' => 'complete']);
                } else {
                    //开营失败 自动退款
                    try {
                        Db::startTrans();
                        $result =  $item->allowField(true)->save(['status' => 'failed']);
                        if(false !== $result){
                            $orderList = $orderModel->where(['brand_id' => $item->brand_id ?? 0, 'data_id' => xilufitness_get_id_value($item->id ?? 0), 'order_type' => 3,
                                'pay_status' => 1, 'order_status' => 1])->select();
                            foreach ($orderList as $key => $val){
                                    $orderResult = $val->save(['order_status' => 4]);
                                    if(false !== $orderResult){
                                        PayService::getInstance(['mini_config' => $this->getConfig($val['brand_id'])])->refundOrder(xilufitness_get_id_value($val['id']),($val['brand_id'] ?? 0));
                                    }
                                    unset($orderResult);
                            }
                        }
                        Db::commit();
                    } catch (\Exception $e){
                        Db::rollback();
                        Log::record("活动开营失败：".$e->getMessage());
                    }
                }
            });
            $current_count = ($i - 1)*$page_size + count($list);
        } while($total_count > $current_count && ++$i);
    }



}