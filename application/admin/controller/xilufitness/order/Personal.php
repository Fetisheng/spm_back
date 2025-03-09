<?php

namespace app\admin\controller\xilufitness\order;

use app\admin\controller\xilufitness\traits\Fitness;
use app\common\controller\Backend;
use think\exception\DbException;
use think\response\Json;

/**
 * 订单列管理
 *
 * @icon fa fa-circle-o
 */
class Personal extends Backend
{
    use Fitness;

    /**
     * Personal模型对象
     * @var \app\admin\model\xilufitness\order\Personal
     */
    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'order_no,trade_no,user.nickname,brand.brand_name';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\order\Personal;

    }


    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            $this->assignconfig("data_id",$this->request->param('data_id/d',0));
            $this->assignconfig('brand_id',$this->request->param('brand_id/d',0));
            $this->assignconfig('shop_id',$this->request->param('shop_id/d',0));
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $fitness_brand_id = $this->getFitnessBrandId();
        $fitness_shop_id = $this->getFitnessShopId();
        $list = $this->model
            ->field(['id','order_no','data_id','course_camp_id','brand_id','shop_id','user_id','coach_id','coupon_amount',
                'pay_amount','pay_status','order_status','pay_time','pay_type','code_num','updatetime','createtime'])
            ->with(['user' => function($query){
                $query->withField(['id','nickname','status','mobile']);
            }, 'brand' => function($query){
                $query->withField(['id','brand_name','status']);
            }, 'shop' => function($query){
                $query->withField(['id','shop_name','status']);
            }, 'courses' => function($query){
                $query->withField(['id','title','status']);
            }, 'coach' => function($query){
                $query->withField(['id','coach_name','status']);
            }])
            ->where($where)
            ->where(function ($query) use($fitness_brand_id,$fitness_shop_id){
                $query->where(['order_type' => 2]);
                if($fitness_brand_id > 0){
                    $query->where('personal.brand_id','eq',$fitness_brand_id);
                }
                if($fitness_shop_id > 0){
                    $query->where('personal.shop_id','eq',$fitness_shop_id);
                }
            })
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    //批量操作(修改状态)
    public function multi($ids = null) {
        return;
    }


}
