<?php

namespace app\admin\controller\xilufitness\user;

use app\admin\controller\xilufitness\traits\Fitness;
use app\common\controller\Backend;
use think\exception\DbException;
use think\response\Json;

/**
 * 用户积分明细
 *
 * @icon fa fa-circle-o
 */
class UserPoint extends Backend
{
    use Fitness;

    /**
     * UserPoint模型对象
     * @var \app\admin\model\xilufitness\user\UserPoint
     */
    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'user.nickname,brand.brand_name';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\user\UserPoint;
        $rule_types = $this->model->getRuleList();
        array_unshift($rule_types,'系统添加');
        $this->assignconfig('rule_types',$rule_types);
        $this->assign('rule_types',$rule_types);
        $this->assign('fitness_brand_id',$this->getFitnessBrandId());
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
            $user_id = $this->request->param('user_id/d', 0);
            if ($user_id > 0) {
                $this->assign('user_id',$user_id);
            }
            $this->assignconfig('user_id',$user_id);
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $fitness_brand_id = $this->getFitnessBrandId();
        $list = $this->model
            ->with(['user' => function($query){
                $query->withField(['id','nickname']);
            }, 'brand' => function($query){
                $query->withField(['id','brand_name']);
            }])
            ->where($where)
            ->where(function ($query) use($fitness_brand_id){
                if($fitness_brand_id > 0){
                    $query->where('user_point.brand_id','eq',$fitness_brand_id);
                }
            })
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
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
