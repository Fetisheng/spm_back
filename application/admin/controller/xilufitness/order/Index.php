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
class Index extends Backend
{

    use Fitness;

    /**
     * Index模型对象
     * @var \app\admin\model\xilufitness\order\Index
     */
    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'user.nickname,user.mobile,order_no,trade_no';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\order\Index;

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
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $fitness_brand_id = $this->getFitnessBrandId();
        $list = $this->model
            ->field(['id','order_no','brand_id','user_id','data_id','trade_no','pay_amount','total_amount',
                'pay_status','order_status','pay_time','pay_type','deletetime','createtime'])
            ->with(['user' => function($query){
               return $query->withField(['nickname','status','mobile']);
            },'brand' => function($query){
               return $query->withField(['brand_name','status']);
            }, 'recharge' => function($query){
                $query->withField(['id','account_amount','recharge_amount','status']);
            }])
            ->where($where)
            ->where(function ($query) use($fitness_brand_id){
                $query->where(['order_type' => 0]);
                if($fitness_brand_id > 0){
                    $query->where('index.brand_id','eq',$fitness_brand_id);
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
