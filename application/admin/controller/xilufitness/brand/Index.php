<?php

namespace app\admin\controller\xilufitness\brand;

use app\admin\controller\xilufitness\traits\Fitness;
use app\common\controller\Backend;
use fast\Random;
use think\Db;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\response\Json;

/**
 * 品牌管理
 *
 * @icon fa fa-circle-o
 */
class Index extends Backend
{
    use Fitness;

    /**
     * Index模型对象
     * @var \app\admin\model\xilufitness\brand\Index
     */
    protected $model = null;
    /**
     * 开启模型验证
     * @var bool
     */
    protected $modelValidate = true;
    /**
     * 开启场景验证
     * @var bool
     */
    protected $modelSceneValidate = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\brand\Index;
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
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $fitness_brand_id = $this->getFitnessBrandId();
        $list = $this->model
            ->where($where)
            ->where(function ($query) use($fitness_brand_id){
                if($fitness_brand_id > 0){
                    $query->where('id','eq',$fitness_brand_id);
                }
            })
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    /**
     * 小程序的配置
     */
    public function config(){
        $brand_id = $this->getFitnessBrandId();
        $configModel = new \app\admin\model\xilufitness\brand\Config();
        if (false === $this->request->isPost()) {
            $navBar = $configModel->getNavBar($brand_id);
            $this->view->assign('navBar',$navBar);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        try {
            Db::startTrans();
            $data = [];
            $configData = [];
            foreach ($params['name'] as $key => $val){
                $paramsExist = $configModel
                    ->where(['brand_id' => $brand_id, 'name' => $val, 'group' => $params['group']])
                    ->field(['id'])
                    ->find();
                $configData[$val] = $params[$val][0] ?? '';
                $data[] = !empty($paramsExist) ? [
                    'id' => $paramsExist['id'], 'name' => $val, 'value' => $params[$val][0] ?? '','tip' => $params['tip'][$key] ?? '',
                    'group' => $params['group'], 'type' => $params['type'][$key] ?? '','title' => $params['title'][$key] ?? '',
                    'rule' => $params['rule'][$key] ?? '','brand_id' => $brand_id ] : [
                    'name' => $val, 'value' => $params['value'][$key] ?? '','brand_id' => $brand_id,
                    'tip' => $params['tip'][$key] ?? '', 'group' => $params['group'], 'type' => $params['type'][$key] ?? '',
                    'title' => $params['title'][$key] ?? '', 'rule' => $params['rule'][$key] ?? ''
                ];
            }
            $result = $configModel->allowField(true)->saveAll($data);
            $flag = $configModel->createConfigFile($configData,$brand_id);
            if (!$flag) {
                $this->error(__('没有权限修改'));
            }
            Db::commit();
        } catch (\Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }
        if(false == $result){
            $this->error(__('No rows were inserted'));
        } else {
            $this->success();
        }
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
