<?php

namespace app\admin\controller\xilufitness\card;

use app\admin\controller\xilufitness\traits\Fitness;
use app\admin\model\xilufitness\card\Category;
use app\admin\model\xilufitness\card\PackageDetails;
use app\common\controller\Backend;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Log;

class CardPackage extends Backend
{
    use Fitness;
    protected $relationSearch = true;

    protected $model = null;

    protected $noNeedRight = ['destroy_package_details','get_category_list'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\card\CardPackage();
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
                ->order($sort, $order)
                ->paginate($limit);
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
        $fitness_brand_id = $this->getFitnessBrandId();
        if (false === $this->request->isPost()) {
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
        $result = false;
        Db::startTrans();
        try {
            $params['brand_id']=$fitness_brand_id;
            $params['createtime'] = time();
            $params['status'] = 1;
            //获取会员卡种类
            $card_category_ids=$params['card_category_ids'];
            $db = Db::name('xilufitness_card_package');
            // 插入数据并获取自增ID
            $insertId = $db->insertGetId($params);
            //根据','分割category_ids
            $category_ids_arr=explode(',', $card_category_ids);
            foreach ($category_ids_arr as $category_id){
                $args['card_package_id'] = $insertId;
                $args['card_category_id'] = $category_id;
                $args['sort'] = 1;
                $args['createtime'] = time();
                $args['updatetime'] = time();
                $args['status'] = 1;
                PackageDetails::create($args);
            }
            $result = true;
            Db::commit();
        } catch (ValidateException $e ) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
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
            $card_category_list = $this->get_category_list($row['card_category_ids']);
            $this->view->assign('row', $row);
            $this->view->assign('card_category_list', $card_category_list);
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
            $params['updatetime'] = time();
            $result = $row->allowField(true)->save($params);
            //清除设置
            PackageDetails::destroy(['card_package_id' => $row['id']]);
            $category_ids_arr=explode(',', $row['card_category_ids']);
            foreach ($category_ids_arr as $category_id){
                $args['card_package_id'] = $row['id'];
                $args['card_category_id'] = $category_id;
                $args['sort'] = 1;
                $args['createtime'] = time();
                $args['updatetime'] = time();
                $args['status'] = 1;
                PackageDetails::create($args);
            }
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
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->model->destroy($ids);
        //清除设置
        $where['card_package_id'] = ['in',$row['id']];
        PackageDetails::destroy($where);
        $this->success();
    }

    public function destroy_package_details($card_category_id)
    {
        PackageDetails::destroy(['card_package_id' => $card_category_id]);

        $package_details = new PackageDetails();
        $package_details->destroy(['card_package_id'=>$card_category_id]);
    }


    public function get_category_list($card_category_ids)
    {
        $category_ids_arr=explode(',', $card_category_ids);
        $card_model = new Category;
        $where['id'] = ['in',$category_ids_arr];
        $where['status'] = '1';
        $cardList = $card_model
            ->field(['id as card_category_id','cardname', 'cardtype', 'cardtypename', 'timescardquota', 'cardprice', 'expiretimes', 'expiretype'])
            ->where($where)
            ->select();

        foreach ($cardList as $card){
            if ($card->cardtype == 2) {
                $card->timescardquota = '-';
            }
            switch ($card->expiretype) {
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
            $card->expiretimes = "$card->expiretimes$time";
        }
        return $cardList;
    }
}