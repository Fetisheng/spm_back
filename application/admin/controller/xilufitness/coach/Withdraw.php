<?php

namespace app\admin\controller\xilufitness\coach;

use app\admin\controller\xilufitness\traits\Fitness;
use app\common\controller\Backend;
use think\exception\DbException;
use think\response\Json;

/**
 * 教练提现记录
 *
 * @icon fa fa-circle-o
 */
class Withdraw extends Backend
{
    use Fitness;
    /**
     * Withdraw模型对象
     * @var \app\admin\model\xilufitness\coach\Withdraw
     */
    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'brand.brand_name,coach.coach_name';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\coach\Withdraw;
        $this->assign('statusList',$this->model->getStatusList());
        $this->assignconfig('statusList',$this->model->getStatusList());
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
            $coach_id = $this->request->param('coach_id/d',0);
            $brand_id = $this->request->param('brand_id/d',0);
            $this->assignconfig('coach_id',$coach_id);
            $this->assignconfig('brand_id',$brand_id);
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $fitness_brand_id = $this->getFitnessBrandId();
        $list = $this->model
            ->with(['brand' => function($query){
                $query->withField(['brand_name']);
            }, 'coach' => function($query){
                $query->withField(['coach_name']);
            }])
            ->where($where)
            ->where(function ($query) use($fitness_brand_id){
                if($fitness_brand_id > 0){
                    $query->where('withdraw.brand_id','eq',$fitness_brand_id);
                }
            })
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    //添加
    public function add(){
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
