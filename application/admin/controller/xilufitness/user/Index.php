<?php

namespace app\admin\controller\xilufitness\user;

use app\admin\controller\xilufitness\traits\Fitness;
use app\common\controller\Backend;
use think\exception\DbException;
use think\response\Json;

/**
 * 会员列管理
 *
 * @icon fa fa-circle-o
 */
class Index extends Backend
{
    use Fitness;

    /**
     * Index模型对象
     * @var \app\admin\model\xilufitness\user\Index
     */
    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'nickname,mobile,brand.brand_name';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\user\Index;
        $this->view->assign("statusList", $this->model->getStatusList());
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
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $fitness_brand_id = $this->getFitnessBrandId();
        $list = $this->model
            ->field(['id','brand_id','nickname','avatar','mobile','gender','point','account',
                'train_day','train_duration','train_count','is_vip','status','createtime'])
            ->with(['brand' => function($query){
                $query->withField(['brand_name']);
            }])
            ->where($where)
            ->where(function ($query) use($fitness_brand_id){
                if($fitness_brand_id > 0){
                    $query->where('brand_id','eq',$fitness_brand_id);
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



}
