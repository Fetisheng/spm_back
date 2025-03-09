<?php


namespace addons\xilufitness\services\user;


use addons\xilufitness\services\BaseService;
use addons\xilufitness\services\ShopService;
use think\Db;

class UserService extends BaseService
{

    protected $model = null;

    protected function __initialize()
    {
        $this->model = new \addons\xilufitness\model\User;
    }

    /**
     * 获取个人基本信息
     * @return array|bool|false|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserInfo(int $brand_id){
        $rechargeModel = new \addons\xilufitness\model\ActivityRecharge;
        $coachModel = new \addons\xilufitness\model\Coach;
        $info = $this->userInfo;
        $rechargeInfo = $rechargeModel
            ->normal()
            ->where(['brand_id' => $brand_id])
            ->field(['id','recharge_amount','account_amount','cut_amount'])
            ->order("recharge_amount asc")
            ->find();
        if(!empty($info)){
            $mobile = $info->getData('mobile');
            $coachInfo = $coachModel
                ->where(['coach_mobile' => $mobile ])
                ->field(['id','coach_name'])
                ->find();
            $info->append(['is_coach']);
            $info->is_coach = !empty($coachInfo) ? 1 : 0;
            //解锁勋章
            $mediaData['user_id'] = $this->auth->id;
            $mediaData['brand_id'] = $this->brand_id;
            \think\Hook::listen('xilufitness_medal_unlocking',$mediaData);
        }
        return ['info' => $info, 'recharge' => $rechargeInfo];
    }

    /**
     * 保存基本信息
     * @param array $params 保存的字段
     * @param int $brand_id 品牌商id
     * @return bool
     */
    public function saveBaseInfo(array $params, int $brand_id){
        $validateName = '\\addons\\xilufitness\\validate\\User';
        $userInfo = $this->model
            ->where(['user_id' => $this->auth->id, 'brand_id' => $brand_id])
            ->find();
        $result = $userInfo
            ->allowField(true)
            ->validateFailException()
            ->validate($validateName)
            ->save($params);
        return $result;
    }

    /**
     * 用户余额变动
     * @param string $title 变动说明
     * @param int $user_id 用户id
     * @param string $data_id 关联表的id
     * @param float $amount 变动余额
     * @param int $amount_type 类型 1 新增 2 减少
     * @param int $account_type 关联类型 0 会员充值 1 团课 2 私教课 3 活动 4 提现
     */
    public function userAccountChange(string $title, int $user_id, string $data_id,
                                      float $amount, int $amount_type, int $account_type,int $brand_id=0){
        if(empty($this->userInfo)){
            $userModel = new \addons\xilufitness\model\User;
            $this->userInfo = $userModel
                ->where(['id' => $user_id])
                ->field(['id','nickname','avatar','gender','mobile','point','account','train_day','train_duration','train_count','is_vip'])
                ->find();
        }
        $account['data_id'] = xilufitness_get_id_value($data_id);
        $account['user_id'] = $user_id;
        $account['brand_id'] = !empty($brand_id) ? $brand_id : $this->brand_id;
        $account['title'] = $title;
        $account['before_account'] = $this->userInfo->account ?? 0;
        $account['amount_type'] = $amount_type;
        if($amount_type == 1){
            $account['after_account'] = bcadd($account['before_account'],$amount,2);
        } else {
            $account['after_account'] = bcsub($account['before_account'],$amount,2);
        }
        $account['amount'] = $amount;
        $account['account_type'] = $account_type;
        return \think\Hook::listen('xilufitness_user_account_change',$account);
    }

    /**
     * 获取积分记录
     */
    public function getMyPointList(){
        $model = new \addons\xilufitness\model\UserPoint;
        $rows = $model
            ->where(['user_id' => $this->getUserId(), 'brand_id' => $this->brand_id])
            ->field(['id','title','point','point_type','createtime'])
            ->order('id desc')
            ->paginate();
        return ['list' => $rows->items(), 'total_count' => $rows->total()];
    }
    /**
     * 获取余额记录
     */
    public function getMyAccountList(){
        $model = new \addons\xilufitness\model\UserAccount;
        $rows = $model
            ->where(['user_id' => $this->getUserId(), 'brand_id' => $this->brand_id])
            ->field(['id','title','amount','amount_type','createtime'])
            ->order('id desc')
            ->paginate();
        return ['list' => $rows->items(), 'total_count' => $rows->total()];
    }

    /**
     * 收藏
     * @param int $id id值
     * @param string $is_type 类型 1 店铺 2 教练
     */
    public function addCollect(int $id,string $is_type,int $shop_id=0){
        $model = new \addons\xilufitness\model\UserCollect;
        $collectExists = $model
            ->where(['user_id' => $this->getUserId(), 'brand_id' => $this->brand_id,
                'data_id' => $id, 'is_type' => $is_type, 'shop_id' => $shop_id])
            ->find();
        if(!empty($collectExists)){
            $result = $collectExists->delete();
            return ['is_collect' => false !== $result ? 0 : 1];
        } else {
            $result = $model->save([
                'user_id' => $this->getUserId(),
                'brand_id' => $this->brand_id,
                'data_id' => $id,
                'is_type' => $is_type,
                'shop_id' => $shop_id
            ]);
            return  ['is_collect' => false !== $result ? 1 : 0];
        }
    }

    /**
     * 判断是否收藏过
     * 门店,教练
     * @param int $data_id 外健
     * @param int $is_type 类型 1 门店 2 教练
     */
    public function getCollectExists($data_id,$is_type,int $shop_id=0){
        $model = new \addons\xilufitness\model\UserCollect;
        $isCollect = $model
            ->where(['user_id' => $this->getUserId(), 'brand_id' => $this->brand_id,
                'data_id' => $data_id, 'is_type' => $is_type, 'shop_id' => $shop_id])
            ->field(['id'])
            ->find();
        return  !empty($isCollect) ? 1 : 0;
    }

    /**
     * 我的收藏
     * @param int $is_type
     * @param $lat 纬度
     * @param $lng 经度
     */
    public function getMyCollect(int $is_type, $lat, $lng){
        $model = new \addons\xilufitness\model\UserCollect;
        if($is_type == 1){
            $list = $model
                ->field(['id','user_id','is_type','data_id'])
                ->with(['shop' => function($query) use($lat,$lng){
                    return $query->withField(['id','shop_name','shop_image','lat','lng','address']);
                }])
                ->where(['user_collect.brand_id' => $this->brand_id, 'user_id' => $this->getUserId(), 'is_type' => 1])
                ->select();
        } else {
            $collects =  $model
                ->field(['id','user_id','is_type','data_id'])
                ->where(['brand_id' => $this->brand_id, 'user_id' => $this->getUserId(), 'is_type' => 2])
                ->select();
            $shop_ids = array_column($collects,'shop_id');
            $shopModel = new \addons\xilufitness\model\Shop;
            $list = $shopModel
                ->field(['id','shop_name','shop_image','address','lat','lng',
                    "(6378.138 * 2 * asin(sqrt(pow(sin((lat * pi() / 180 - ".$lat." * pi() / 180) / 2),2) + cos(lat * pi() / 180) * cos(".$lat." * pi() / 180) * pow(sin((lng * pi() / 180 - ".$lng." * pi() / 180) / 2),2))) * 1000) as distance"])
                ->where(['id' => ['in', $shop_ids ?? [-1] ], 'brand_id' => $this->brand_id ])
                ->order("distance asc")
                ->select();
            foreach ($list as $key => $val){
                $val->append(['coach']);
                $val->coach = ShopService::getInstance()->getCoach(xilufitness_get_id_value($val['id'] ?? 0));
            }
        }
        return ['list' => $list];
    }

    /**
     * 获取我的勋章
     * @param int $page 分页码
     * @return array
     */
    public function getMyMediaList(){
        $mediaModel = new \addons\xilufitness\model\ActivityMedia;
        $userMediaModel = new \addons\xilufitness\model\UserMedia;
        $rows = $mediaModel
            ->where(['status' => 'normal', 'brand_id' => $this->brand_id])
            ->field(['id','thumb_image','thumb_active_image','medal_name','description'])
            ->order("class_time asc")
            ->paginate();
        $list = $rows->items();
        foreach ($list as $key => $value){
            $value->append(['is_active']);
            $is_active = $userMediaModel
                ->where(['user_id' => $this->getUserId(), 'brand_id' => $this->brand_id, 'media_id' => xilufitness_get_id_value($value['id'] ?? 0)])
                ->field(['id'])
                ->find();
            $value->is_active = !empty($is_active) ? 1 : 0;
        }
        return ['list' => $list, 'total_count' => $rows->total() ];
    }

    /**
     * 获取排行排名
     * @param int $page 分页码
     * @return array
     */
    public function getMyRanking(){
        $userModel = new \addons\xilufitness\model\User;
        $rows = $userModel
            ->where(['brand_id' => $this->brand_id])
            ->field(['id','nickname','avatar','train_duration'])
            ->order("train_duration desc")
            ->paginate();
        $prefix = config('database.prefix');
        $ranking_sql = "SELECT b.id,b.nickname,b.avatar,b.train_duration,b.rownum FROM(SELECT t.*, @rank_no := @rank_no + 1 AS rownum FROM (SELECT @rank_no := 0) r,(SELECT * FROM ".$prefix."xilufitness_user ORDER BY train_duration DESC) AS t ) AS b WHERE b.user_id =".$this->getUserId();
        $userInfo = $userModel->query($ranking_sql);
        if(!empty($userInfo[0])){
            $userInfo[0]['train_duration'] = round(($userInfo[0]['train_duration']/60),1);
        }
        return ['list' => $rows->items(), 'total_count' => $rows->total(), 'userInfo' => $userInfo[0] ?? ''];
    }


}