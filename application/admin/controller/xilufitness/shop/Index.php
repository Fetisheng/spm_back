<?php

namespace app\admin\controller\xilufitness\shop;

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
 * 门店列管理
 *
 * @icon fa fa-circle-o
 */
class Index extends Backend
{
    use Fitness;

    /**
     * Index模型对象
     * @var \app\admin\model\xilufitness\shop\Index
     */
    protected $model = null;

    /**
     * @var string
     * 快捷搜索字段
     */
    protected $searchFields = 'shop_name,shop_mobile,username,address';

    /**
     * @var bool
     * 模型验证开启
     */
    protected $modelValidate = true;

    /**
     * @var bool
     * 场景验证开启
     */
    protected $modelSceneValidate = true;

    protected $relationSearch = true;

    protected $noNeedRight = ['get_area'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\shop\Index;
        $this->view->assign("statusList", $this->model->getStatusList());
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
        $fitness_shop_id = $this->getFitnessShopId();
        $list = $this->model
            ->with(['brand' => function($query){
                $query->withField(['id','brand_name','status']);
            }])
            ->where($where)
            ->where(function ($query) use($fitness_brand_id,$fitness_shop_id){
                if($fitness_brand_id > 0){
                    $query->where('brand_id','eq',$fitness_brand_id);
                }
                if($fitness_shop_id > 0){
                    $query->where('index.id','eq',$fitness_shop_id);
                }
            })
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    /**
     * 添加
     *
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
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
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }
            $result = $this->model->allowField(true)->save($params);
            $group_id = $this->addGroup();
            $brand_id = $params['brand_id'] ?? 0;
            $admin_id = $this->createAdminAccount($group_id,$params['username'],$params['shop_name'],$params['password'],$params['shop_mobile']);
            $this->addAdminAccess($brand_id,$admin_id,$this->model->id);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
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
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
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
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $shopValidate = \think\Loader::validate($name);
                $shopValidate->rule([
                    'brand_id' => 'require',
                    'username' => 'require|regex:\w{3,30}|unique:xilufitness_shop,username,' . $row->id,
                    'shop_name' => 'require|unique:xilufitness_shop,shop_name,' . $row->id,
                    'shop_mobile' => 'require|unique:xilufitness_shop,shop_mobile,' . $row->id,
                    'shop_image' => 'require',
                    'shop_images' => 'require',
                    'province_id' => 'require',
                    'city_id' => 'require',
                    'area_id' => 'require',
                    'address' => 'require',
                ]);
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $result = $row->allowField(true)->save($params);
            $group_id = $this->addGroup();
            $this->createAdminAccount($group_id,$params['username'],$params['shop_name'],$params['password'] ?? '',$params['shop_mobile']);

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
     * 获取城市信息
     */
    public function get_area(){
        $params = $this->request->get("row/a");
        if (!empty($params)) {
            $province = isset($params['province']) ? $params['province'] : null;
            $city = isset($params['city']) ? $params['city'] : null;
        } else {
            $province = $this->request->get('province');
            $city = $this->request->get('city');
        }
        $where = ['pid' => 0, 'level' => 1];
        $provincelist = null;
        if ($province !== null) {
            $where['pid'] = $province;
            $where['level'] = 2;
            if ($city !== null) {
                $where['pid'] = $city;
                $where['level'] = 3;
            }
        }
        $provincelist = Db::name('xilufitness_area')->where($where)->field('id as value,name')->select();
        $this->success('', '', $provincelist);
    }

    /**
     * 添加账号
     * @param int $group_id 权限组id
     * @param string $username
     * @param string $nickname
     * @param string $password
     * @return bool|mixed
     */
    private function createAdminAccount(int $group_id,string $username, string $nickname,string $password, string $mobile){
        $params['username'] = $username;
        $params['nickname'] = $nickname;
        $params['mobile'] = $mobile ?? '';
        if(!empty($password)){
            $params['salt'] = Random::alnum();
            $params['password'] = $this->auth->getEncryptPassword($password, $params['salt']);
        }
        $params['avatar'] = '/assets/img/avatar.png'; //设置新管理员默认头像。
        $model = model('Admin');
        $adminInfo = $model->where(['username' => $username])->find();
        if(!empty($adminInfo)) return $adminInfo['id'];
        $result = $model->save($params);
        if(false !== $result){
            $dataset= ['uid' => $model->id, 'group_id' => $group_id];
            model('AuthGroupAccess')->allowField(true)->save($dataset);
            return $model->id;
        } else {
            throw new Exception(__('Account creation failed'));
        }
    }

    /**
     * 添加账号中间表信息
     * @param int $brand_id 品牌商id
     * @param int $admin_id 账号id
     * @param int $shop_id 门店id
     */
    private function addAdminAccess(int $brand_id,int $admin_id,int $shop_id){
        $accessModel = new \app\admin\model\xilufitness\brand\AdminAccess;
        $accessInfo = $accessModel->where(['brand_id' => $brand_id, 'admin_id' => $admin_id])->find();
        if(empty($accessInfo)){
            $accessResult = $accessModel->save(['admin_id' => $admin_id, 'brand_id' => $brand_id, 'account_type' => 2,'shop_id' => $shop_id]);
        } else {
            $accessResult = $accessInfo->save(['admin_id' => $admin_id, 'brand_id' => $brand_id, 'account_type' => 2, 'shop_id' => $shop_id]);
        }
        if(!$accessResult){
            throw new Exception(__('Account creation failed'));
        }
        return $accessResult;
    }


    /**
     * 添加权限组
     * @return int
     */
    private function addGroup(){
        $model = new \app\admin\model\AuthGroup;
        $groupModel = new \app\admin\model\xilufitness\brand\AuthGroup;
        $adminBrandGroup = $groupModel->where(['is_type' => 2])->field(['group_id'])->find();
        $groupExist = $model->where(['id' => $adminBrandGroup['group_id'] ?? 0, 'status' => 'normal'])->find();
        if(empty($groupExist)){
            $data['name'] = '门店管理组';
            $data['pid'] = $model->where(['pid' => 0])->value('id');
            $data['rules'] = implode(",",$this->getRuleMenu());
            $data['status'] = 'normal';
            $result = $model->allowField(true)->save($data);
            if(false !== $result){
                if(!empty($adminBrandGroup)){
                    $adminBrandGroup->allowField(true)->save(['group_id' => $model->id ?? 0, 'is_type' => 2]);
                } else {
                    $groupModel->allowField(true)->save(['group_id' => $model->id ?? 0, 'is_type' => 2]);
                }
                return $model->id;
            }
            return 0;
        }
        return $adminBrandGroup['group_id'] ?? 0;

    }

    /**
     * 获取权限菜单
     * @retrun array
     */
    private function getRuleMenu(){
        $model = new \app\admin\model\AuthRule;
        $list = $model
            ->where(['name' => ['like','%xilufitness%']])
            ->field(['id','name'])
            ->select();
        $menu_list = [];
        array_walk($list,function ($val,$key) use(&$menu_list){
            //项目 控制台
            if(strpos($val['name'],'xilufitness/analyse') !== false || $val['name'] == 'xilufitness'){
                $menu_list[] = $val;
            }
            //门店
            if(strpos($val['name'],'xilufitness/shop') !== false && $val['name'] != 'xilufitness/shop/index/add'){
                $menu_list[] = $val;
            }
            //教练相关
            if($val['name'] == 'xilufitness/coach' || strpos($val['name'],'xilufitness/coach/index') !== false ||
            strpos($val['name'],'xilufitness/coach/account') !== false || strpos($val['name'],'xilufitness/coach/cash') !== false ||
            strpos($val['name'],'xilufitness/coach/withdraw') !== false){
                $menu_list[] = $val;
            }
            //排课相关
            if(strpos($val['name'],'xilufitness/work') !== false){
                $menu_list[] = $val;
            }
            //订单相关
            if($val['name'] == 'xilufitness/order' || strpos($val['name'],'xilufitness/order/course') !== false ||
                strpos($val['name'],'xilufitness/order/personal') !== false || strpos($val['name'],'xilufitness/order/camp') !== false ){
                $menu_list[] = $val;
            }
            //会员相关
            if($val['name'] == 'xilufitness/user' || strpos($val['name'],'xilufitness/user/index') !== false ||
            strpos($val['name'],'xilufitness/user/account') || strpos($val['name'],'xilufitness/user/collect') ||
            strpos($val['name'],'xilufitness/user/comment') !== false || strpos($val['name'],'xilufitness/user/coupon') !== false ||
            str_contains($val['name'],'xilufitness/user/media') !== false || strpos($val['name'],'xilufitness/user/user_point') !== false){
                $menu_list[] = $val;
            }
        });
        $menu = array_column($menu_list,'id');
        sort($menu);
        return $menu;
    }



}
