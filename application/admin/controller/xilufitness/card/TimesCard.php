<?php

namespace app\admin\controller\xilufitness\card;

use app\admin\controller\xilufitness\traits\Fitness;
use app\admin\model\xilufitness\card\Category;
use app\common\controller\Backend;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

class TimesCard extends Backend
{
    use Fitness;
    protected $relationSearch = true;
    protected $searchFields = 'id,cardtype';

    /**
     * @var Category
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new Category;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->assign('fitness_brand_id',$this->getFitnessBrandId());
    }

    /**
     * 查看
     * @throws Exception
     */
    public function index()
    {
        $fitness_brand_id = $this->getFitnessBrandId();
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->where($where)
                ->with(['brand' => function($query){
                    $query->withField(['id','brand_name','status']);
                }])
                ->where(function ($query) use($fitness_brand_id){
                    if($fitness_brand_id > 0){
                        $query->where('brand_id','eq',$fitness_brand_id);
                    }
                })
                ->where('cardtype', '=', '1')
                ->order($sort, $order)
                ->paginate($limit);
            foreach ($list as $k => $v) {
                switch ($v->expiretype) {
                    case '1':
                        $time = "天";
                        break;
                    case '2':
                        $time = "周";
                        break;
                    case '3':
                        $time = "个月";
                        break;
                    case '4':
                        $time = "年";
                        break;
                }
                $v->expiretimes = "$v->expiretimes$time";
            }
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     * @throws Exception
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }

        return parent::add();
    }

    /**
     * 编辑
     * @throws Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        $row = $this->model->get($ids);
        \think\Log::info($row);
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->model->destroy($ids);
        $this->success();
    }

}