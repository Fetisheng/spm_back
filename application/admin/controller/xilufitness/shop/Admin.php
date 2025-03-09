<?php


namespace app\admin\controller\xilufitness\shop;


use app\admin\controller\xilufitness\traits\Fitness;
use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use app\common\controller\Backend;
use fast\Random;
use fast\Tree;
use think\Db;
use think\exception\DbException;
use think\response\Json;
use think\Validate;

class Admin extends Backend
{
    use Fitness;

    /**
     * @var \app\admin\model\xilufitness\brand\AdminAccess
     */
    protected $model = null;

    protected $adminModel = null;

    protected $selectpageFields = 'admin.id,admin.username,admin.nickname,admin.avatar';
    protected $searchFields = 'admin_access.id,admin.id,admin.username,admin.nickname';

    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\xilufitness\brand\AdminAccess();
        $this->adminModel = new \app\admin\model\Admin();
        $this->childrenAdminIds = $this->auth->getChildrenAdminIds($this->auth->isSuperAdmin());
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds($this->auth->isSuperAdmin());

        $groupList = collection(AuthGroup::where('id', 'in', $this->childrenGroupIds)->select())->toArray();

        Tree::instance()->init($groupList);
        $groupdata = [];
        if ($this->auth->isSuperAdmin()) {
            $result = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0));
            foreach ($result as $k => $v) {
                $groupdata[$v['id']] = $v['name'];
            }
        } else {
            $result = [];
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n) {
                $childlist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['id']));
                $temp = [];
                foreach ($childlist as $k => $v) {
                    $temp[$v['id']] = $v['name'];
                }
                $result[__($n['name'])] = $temp;
            }
            $groupdata = $result;
        }

        $this->view->assign('groupdata', $groupdata);
        $this->assignconfig("admin", ['id' => $this->auth->id]);
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
        $childrenGroupIds = $this->childrenGroupIds;
        $groupName = AuthGroup::where('id', 'in', $childrenGroupIds)
            ->column('id,name');
        $authGroupList = AuthGroupAccess::where('group_id', 'in', $childrenGroupIds)
            ->field('uid,group_id')
            ->select();

        $adminGroupName = [];
        foreach ($authGroupList as $k => $v) {
            if (isset($groupName[$v['group_id']])) {
                $adminGroupName[$v['uid']][$v['group_id']] = $groupName[$v['group_id']];
            }
        }
        $groups = $this->auth->getGroups();
        foreach ($groups as $m => $n) {
            $adminGroupName[$this->auth->id][$n['id']] = $n['name'];
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $fitness_brand_id = $this->getFitnessBrandId();
        $fitness_shop_id = $this->getFitnessShopId();
        $rows = $this->model
            ->with(['admin' => function($query){
                $query->withField(['id','username','nickname','avatar','mobile','logintime','status','createtime']);
            }, 'brand' => function($query){
                $query->withField(['brand_name']);
            }])
            ->where($where)
            ->where(function ($query) use($fitness_brand_id,$fitness_shop_id){
                $query->where('account_type','eq',2);
                if($fitness_brand_id > 0){
                    $query->where('brand_id','eq',$fitness_brand_id);
                }
                if($fitness_shop_id > 0){
                    $query->where('shop_id','eq',$fitness_shop_id);
                }
            })
            ->order($sort, $order)
            ->paginate($limit);
        $list = $rows->items();

        foreach ($list as $k => &$v) {
            $groups = isset($adminGroupName[$v['admin']['id']]) ? $adminGroupName[$v['admin']['id']] : [];
            $v['admin']['groups'] = implode(',', array_keys($groups));
            $v['admin']['groups_text'] = implode(',', array_values($groups));
        }

        $result = ['total' => $rows->total(), 'rows' => $list];
        return json($result);
    }


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            if ($params) {
                $accessModel = new \app\admin\model\xilufitness\brand\AdminAccess;
                Db::startTrans();
                try {
                    if (!Validate::is($params['password'], '\S{6,30}')) {
                        exception(__("Please input correct password"));
                    }
                    $params['salt'] = Random::alnum();
                    $params['password'] = $this->auth->getEncryptPassword($params['password'], $params['salt']);
                    $params['avatar'] = '/assets/img/avatar.png'; //设置新管理员默认头像。
                    $brand_id = $params['brand_id'] ?? 0;
                    unset($params['brand_id']);
                    $result = $this->adminModel->validate('Admin.add')->allowField(true)->save($params);
                    if ($result === false) {
                        exception($this->adminModel->getError());
                    }
                    $group = $this->request->post("group/a");

                    //过滤不允许的组别,避免越权
                    $group = array_intersect($this->childrenGroupIds, $group);
                    if (!$group) {
                        exception(__('The parent group exceeds permission limit'));
                    }

                    $dataset = [];
                    foreach ($group as $value) {
                        $dataset[] = ['uid' => $this->adminModel->id, 'group_id' => $value];
                    }
                    model('AuthGroupAccess')->saveAll($dataset);
                    if(!empty($brand_id)){
                        $accessModel->allowField(true)->save([
                            'admin_id' => $this->adminModel->id,
                            'brand_id' => $brand_id,
                            'account_type' => 2,
                            'shop_id' => $params['shop_id'] ?? 0
                        ]);
                    }
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                $this->success();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $adminAccessInfo = $this->model->get(['id' => $ids]);
        $row = $this->adminModel->get(['id' => $adminAccessInfo['admin_id'] ?? 0]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $row->append(['brand_id','shop_id']);
        $row->brand_id = $adminAccessInfo['brand_id'];
        $row->shop_id = $adminAccessInfo['shop_id'];
        if (!in_array($row->id, $this->childrenAdminIds)) {
            $this->error(__('You have no permission'));
        }
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            if ($params) {
                Db::startTrans();
                try {
                    if ($params['password']) {
                        if (!Validate::is($params['password'], '\S{6,30}')) {
                            exception(__("Please input correct password"));
                        }
                        $params['salt'] = Random::alnum();
                        $params['password'] = $this->auth->getEncryptPassword($params['password'], $params['salt']);
                    } else {
                        unset($params['password'], $params['salt']);
                    }
                    //这里需要针对username和email做唯一验证
                    $adminValidate = \think\Loader::validate('Admin');
                    $adminValidate->rule([
                        'username' => 'require|regex:\w{3,30}|unique:admin,username,' . $row->id,
                        'email'    => 'require|email|unique:admin,email,' . $row->id,
                        'mobile'   => 'regex:1[3-9]\d{9}|unique:admin,mobile,' . $row->id,
                        'password' => 'regex:\S{32}',
                    ]);
                    $result = $row->validate('Admin.edit')->allowField(true)->save($params);
                    if ($result === false) {
                        exception($row->getError());
                    }

                    // 先移除所有权限
                    model('AuthGroupAccess')->where('uid', $row->id)->delete();

                    $group = $this->request->post("group/a");

                    // 过滤不允许的组别,避免越权
                    $group = array_intersect($this->childrenGroupIds, $group);
                    if (!$group) {
                        exception(__('The parent group exceeds permission limit'));
                    }

                    $dataset = [];
                    foreach ($group as $value) {
                        $dataset[] = ['uid' => $row->id, 'group_id' => $value];
                    }
                    model('AuthGroupAccess')->saveAll($dataset);
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                $this->success();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $grouplist = $this->auth->getGroups($row['id']);
        $groupids = [];
        foreach ($grouplist as $k => $v) {
            $groupids[] = $v['id'];
        }
        $this->view->assign("row", $row);
        $this->view->assign("groupids", $groupids);
        return $this->view->fetch();
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
        if ($ids) {
            $admin_ids = $this->model->where('id','in',$ids)->where(['account_type' => 2])->column('admin_id');
            $ids = array_intersect($this->childrenAdminIds, array_filter(explode(',', $admin_ids)));
            // 避免越权删除管理员
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this->adminModel->where('id', 'in', $ids)->where('id', 'in', function ($query) use ($childrenGroupIds) {
                $query->name('auth_group_access')->where('group_id', 'in', $childrenGroupIds)->field('uid');
            })->select();
            if ($adminList) {
                $deleteIds = [];
                foreach ($adminList as $k => $v) {
                    $deleteIds[] = $v->id;
                }
                $deleteIds = array_values(array_diff($deleteIds, [$this->auth->id]));
                if ($deleteIds) {
                    Db::startTrans();
                    try {
                        $this->adminModel->destroy($deleteIds);
                        model('AuthGroupAccess')->where('uid', 'in', $deleteIds)->delete();
                        Db::commit();
                    } catch (\Exception $e) {
                        Db::rollback();
                        $this->error($e->getMessage());
                    }
                    $this->success();
                }
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('You have no permission'));
    }

    /**
     * 批量更新
     * @internal
     */
    public function multi($ids = "")
    {
        // 管理员禁止批量操作
        $this->error();
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