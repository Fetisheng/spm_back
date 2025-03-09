<?php

namespace app\admin\controller\xilufitness\card;

use app\admin\controller\xilufitness\traits\Fitness;
use app\admin\model\xilufitness\card\CardVerificationRecords;
use app\admin\model\xilufitness\card\Category;
use app\common\controller\Backend;
use think\Exception;

class CardCheck extends Backend
{
    use Fitness;
    protected $relationSearch = true;
    /**
     * @var Category
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new CardVerificationRecords();
        $this->assign('fitness_brand_id',$this->getFitnessBrandId());
        $this->assign('fitness_shop_id',$this->getFitnessShopId());
    }

    /**
     * 查看
     * @throws Exception
     */
    public function index()
    {
        $fitness_brand_id = $this->getFitnessBrandId();
        $fitness_shop_id = $this->getFitnessShopId();
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list= $this->model
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
                ->where(function ($query) use($fitness_brand_id){
                    if($fitness_brand_id > 0){
                        $query->where('card_verification_records.brand_id','eq',$fitness_brand_id);
                    }
                })
                ->where(function ($query) use($fitness_shop_id){
                    if($fitness_shop_id > 0){
                        $query->where('shop_id','eq',$fitness_shop_id);
                    }
                })
                ->order($sort, $order)
                ->paginate($limit);
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
            $result = array("total" => $list->total(), "rows" => $list->items());
            return json($result);
        }
        return $this->view->fetch();
    }
}