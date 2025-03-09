<?php

namespace app\admin\controller\xilufitness\card;

use app\admin\controller\xilufitness\traits\Fitness;
use app\admin\model\xilufitness\card\CardVerificationRecords;
use app\admin\model\xilufitness\card\Category;
use app\common\controller\Backend;
use think\Db;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Log;
use think\Session;

class UserCard extends Backend
{
    use Fitness;
    protected $relationSearch = true;
    protected $searchFields = 'id';
    protected $noNeedRight = ['get_card_category','get_category_info'];

    /**
     * @var UserCard
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\card\UserCard;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("payList", $this->model->getPayList());
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
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {

            //如果发送的来源是SelectPage，则转发到SelectPage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->with(['category' => function($query){
                    $query->withField(['cardname','cardtype','cardtypename']);
                },'user' => function($query){
                    $query->withField(['nickname','avatar','mobile']);
                },'brand' => function($query){
                    $query->withField(['id','brand_name','status']);
                },'shop' => function($query){
                    $query->withField(['id','shop_name']);
                }])
                ->where(function ($query) use($fitness_brand_id){
                    if($fitness_brand_id > 0){
                        $query->where('user_card.brand_id','eq',$fitness_brand_id);
                    }
                })
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $k => $v) {
                if($v->card_type == 2){
                    unset($v->left_times_count);
                    unset($v->share_times);
                    unset($v->already_share_times);
                    unset($v->left_share_times);
                }
            }

            $result = array("total" => $list->total(), "rows" => $list->items());
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
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
            $params['open_card_time'] = date('Y-m-d H:i:s');
            $params['createtime'] = time();
            $effective_date=$params['effective_date'];

            //获取会员卡种类
            $card_category_id=$params['card_category_id'];
            $category = Category::get($card_category_id);
            $params['left_times_count']=$category->timescardquota;
            $params['card_type']=$category->cardtype;
            if ($category->cardtype == 1 || $category->cardtype == 4){
                $params['share_times']=$category->sharetimes;
                $params['already_share_times']=0;
                $params['left_share_times']=$category->sharetimes;
            }

            //有效时长
            $expire_times = $category->expiretimes;
            //有效期类型
            $expire_type = $category->expiretype;

            //计算会员卡到期时间
            $expire_date = null;
            switch ($expire_type) {
                case '1':
                    //天
                    $expire_date = date('Y-m-d', strtotime("+$expire_times day", strtotime($effective_date)));
                    break;
                case '2':
                    //周
                    $expire_date = date('Y-m-d', strtotime("+$expire_times week", strtotime($effective_date)));
                    break;
                case '3':
                    //月
                    $expire_date = date('Y-m-d', strtotime("+$expire_times month", strtotime($effective_date)));
                    break;
                case '4':
                    //年
                    $expire_date = date('Y-m-d', strtotime("+$expire_times year", strtotime($effective_date)));
                    break;
            }
            $params['expire_time'] = $expire_date;

            $db = Db::name('xilufitness_user_card');
            // 插入数据并获取自增ID
            $insertId = $db->insertGetId($params);
            //生成会员卡卡号
            $card_no=10000000+$insertId;
            $up_info['card_no'] = $card_no;
            $row = $this->model->get($insertId);
            $result = $row->save($up_info);
            Db::commit();
        } catch (ValidateException | DbException $e ) {
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
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if (false === $this->request->isPost()) {
            $user_card = $this->model
                ->with(['category' => function($query){
                    $query->withField(['cardname','cardtype','cardtypename']);
                },'user' => function($query){
                    $query->withField(['nickname','avatar','mobile']);
                }])
                ->where('user_card.id','=',$ids)
                ->find();
            $this->view->assign('row', $user_card);
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
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->model->destroy($ids);
        $this->success();
    }

    /**
     * 获取会员卡类别
     * @throws Exception
     */
    public function get_card_category(){
        $fitness_brand_id = $this->getFitnessBrandId();
        $card_type = $this->request->get('card_type');
        $card_model = new Category;
        if (!empty($card_type)) {
            $cardList = $card_model
                ->where(function ($query) use($fitness_brand_id){
                    if($fitness_brand_id > 0){
                        $query->where('brand_id','eq',$fitness_brand_id);
                    }
                })
                ->where('cardtype', '=', $card_type)
                ->where('status', '=', '1')
                ->field('id as value,cardname as name')
                ->select();
        } else {
            $cardList = $card_model
                ->where(function ($query) use($fitness_brand_id){
                    if($fitness_brand_id > 0){
                        $query->where('brand_id','eq',$fitness_brand_id);
                    }
                })
                ->where('status', '=', '1')
                ->order('cardtype', 'asc')
                ->field('id as value,cardname as name')
                ->select();
        }
        $this->success('', '', $cardList);
    }

    /**
     * 获取会员卡
     * @throws Exception
     */
    public function get_category_info(){
        $category_id = $this->request->param('category_id/d');
        $card_model = new Category;
        $cardInfo = $card_model
            ->where('id', '=', $category_id)
            ->find();

        switch ($cardInfo->expiretype) {
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
        $cardInfo->expiretimes = "$cardInfo->expiretimes$time";
        $this->success('', '', $cardInfo);
    }


    /**
     * 一键核销
     * @throws Exception
     */
    public function check($ids = null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            Db::startTrans();
            try {
                $user_card_id = $params['user_card_id'];
                $shop_id = $params['shop_id'];
                Log::log($shop_id);
                $user_card = $this->model->get($user_card_id);
                if($user_card->effective_date > date('Y-m-d')){
                    $this->error(__('核销失败-会员卡未生效！'));
                }

                if($user_card->expire_time < date('Y-m-d')){
                    $this->error(__('核销失败-会员卡已过期！'));
                }

                if($user_card->card_type==1 || $user_card->card_type==4){
                    if ($user_card->left_times_count <= 0) {
                        $this->error(__('核销失败-会员卡剩余次数不足！'));
                    }
                    //剩余额度-1
                    $user_card['left_times_count'] = $user_card['left_times_count'] - 1;
                    $user_card['updatetime'] = time();
                    $user_card->save();
                }

                $admin = Session::get('admin');
                $admin_user_id = $admin['id'];
                //保存核销记录
                Log::log("保存核销记录");
                $params['brand_id'] = $this->getFitnessBrandId();
                $params['user_card_id'] = $user_card_id;
                $params['shop_id'] = $shop_id;
                $params['user_id'] = $user_card->user_id;
                $params['admin_user_id'] = $admin_user_id;
                $params['check_time'] = date('Y-m-d H:i:s');
                $params['check_type'] = 2;
                $params['createtime'] = time();
                $params['status'] = 1;
                $records = new CardVerificationRecords();
                $records->save($params);
                Log::log("保存完成");
                Db::commit();
                $this->success('核销成功');
            } catch (ValidateException $e) {
                Log::log("核销失败！");
                Db::rollback();
                $this->error($e->getMessage());
            }
        }else{
            Log::log("一键核销");
            $where['user_card.id'] = $ids;
            $card_info = $this->model
                ->with(['category' => function ($query) {
                    $query->withField(['cardname', 'cardtype', 'cardtypename']);
                }, 'user' => function ($query) {
                    $query->withField(['nickname', 'avatar', 'mobile']);
                }, 'brand' => function ($query) {
                    $query->withField(['id', 'brand_name', 'status']);
                }])
                ->where($where)
                ->find();
            $this->view->assign("shopList", $this->get_shop_list());
            $this->view->assign("row", $card_info);
            return $this->view->fetch();
        }
    }

    /**
     * 获取小程序列表
     * @throws Exception
     */
    public function get_shop_list(){
        $fitness_brand_id = $this->getFitnessBrandId();
        $fitness_shop_id = $this->getFitnessShopId();
        $shop = new \app\admin\model\xilufitness\shop\Index();
        $brandList = $shop
            ->where('status', '=', 'normal')
            ->field('id as value,shop_name as name')
            ->where(function ($query) use($fitness_brand_id,$fitness_shop_id){
                if($fitness_brand_id > 0){
                    $query->where('brand_id','eq',$fitness_brand_id);
                }
                if($fitness_shop_id > 0){
                    $query->where('index.id','eq',$fitness_shop_id);
                }
            })
            ->select();
        foreach ($brandList as $k => &$v) {
            $v['value'] = __($v['value']);
            $v['name'] = __($v['name']);
        }
        Log::log($brandList);
        return $brandList;
    }
}