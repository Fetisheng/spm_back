<?php

namespace addons\xilufitness\services;

use addons\xilufitness\model\AdminAccess;
use addons\xilufitness\model\CardCategory;
use addons\xilufitness\model\CardPackage;
use addons\xilufitness\model\CardVerificationRecords;
use addons\xilufitness\model\PackageDetails;
use addons\xilufitness\model\TimesCardShare;
use addons\xilufitness\model\UserCard;
use PDOStatement;
use think\Collection;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Log;
use think\Session;

class CardService extends BaseService
{
    protected $model = null;

    protected function __initialize()
    {
        $this->model = new UserCard();
    }

    public function getUserCardList($card_type)
    {
        $result = array();
        $where['user_card.brand_id'] = $this->brand_id;
        $where['user_card.user_id'] = $this->getUserId();
        if (!empty($card_type)) {
            $where['category.cardtype'] = $card_type;
        }
        $card_list= $this->model
            ->field(['id as user_card_id','user_id','brand_id','card_no','card_type','left_times_count','open_card_time','effective_date','expire_time','already_share_times','left_share_times','status'])
            ->with(['category' => function($query){
                $query->withField(['cardname', 'cardtype', 'cardtypename', 'card_avatar', 'card_desc', 'timescardquota', 'sharetimes', 'cardprice', 'expiretimes', 'expiretype', 'shop_name', 'apply_shop', 'apply_course', 'use_limits']);
            },'shop' => function($query){
                $query->withField(['id', 'shop_name']);
            }])
            ->where($where)
            ->order('user_card.id', 'desc')
            ->select();
        $list_01 = array();
        $list_02 = array();
        $list_03 = array();

        foreach ($card_list as $k => $v) {
            switch ($v->category['expiretype']) {
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
            $v->category['expiretimes'] = $v->category['expiretimes'] . $time;

            if ($v->card_type == 1) {
                $list_01[] = $v;
            }
            if ($v->card_type == 2) {
                unset($v->timescardquota);
                unset($v->sharetimes);
                $list_02[] = $v;
            }
            if ($v->card_type == 4) {
                $list_03[] = $v;
            }
        }

        if (sizeof($list_01) > 0) {
            $result['times_card'] = $list_01;
        }
        if (sizeof($list_02) > 0) {
            $result['term_card'] = $list_02;
        }
        if (sizeof($list_03) > 0) {
            $result['course_card'] = $list_03;
        }

        if(empty($card_type) || $card_type==3){
            $times_card_share = new TimesCardShare();
            $wh['to_user_id'] = $this->getUserId();
            $once_card = $times_card_share
                ->field(['id as share_id','user_card_id','card_category_id','from_user_id','to_user_id','share_time','receive_time','share_expire_time','status'])
                ->where($wh)
                ->whereIn('times_card_share.status',[1,3])
                ->with(['fromUser' => function($query){
                    $query->withField(['nickname','avatar']);
                },'category' => function($query){
                    $query->withField(['cardname', 'cardtype', 'cardtypename', 'card_avatar', 'card_desc', 'cardprice', 'expiretimes', 'expiretype', 'shop_name', 'apply_shop', 'apply_course', 'use_limits']);
                },'shop' => function($query){
                    $query->withField(['id', 'shop_name']);
                }])
                ->select();
            foreach ($once_card as $k => $v) {
                $v->cardname = "单次卡";
                switch ($v->category['expiretype']) {
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
                $v->category['expiretimes'] = $v->category['expiretimes'] . $time;
                switch ($v->status) {
                    case '1':
                        $v->status_name = "未使用";
                        break;
                    case '3':
                        $v->status_name = "已使用";
                        break;
                }
            }
            $result['once_card'] = $once_card;
        }

        return $result;
    }

    public function buyUserCard($card_category_id,$shop_id)
    {
        if (empty($card_category_id)) {
            $this->resultError("会员卡类型ID不能为空");
        }
        $this->buyUserCardByCategoryId($card_category_id,$shop_id);
    }

    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function buyUserCardPackage($card_package_id, $shop_id)
    {
        if (empty($card_package_id)) {
            $this->resultError("会员卡套餐ID不能为空");
        }

        $package_info = CardPackage::get($card_package_id);

        if (empty($package_info)) {
            $this->resultError("会员卡套餐ID不存在");
        }

        $packageDetails = new PackageDetails();
        $where['card_package_id'] = $card_package_id;
        $details = $packageDetails
            ->field(['id as package_details_id', 'card_package_id', 'card_category_id'])
            ->where($where)
            ->select();
        foreach ($details as $k => $v) {
            $this->buyUserCardByCategoryId($v->card_category_id,$shop_id);
        }
    }


    public function buyUserCardByCategoryId($card_category_id,$shop_id)
    {
        $params['brand_id'] = $this->brand_id;
        $params['user_id'] = $this->getUserId();
        $params['card_category_id'] = $card_category_id;
        $params['shop_id'] = $shop_id;
        $params['open_card_time'] = date('Y-m-d H:i:s');
        $params['createtime'] = time();
        $params['effective_date']= date('Y-m-d');
        try {
            Db::startTrans();
            //获取会员卡种类
            $category = CardCategory::get($card_category_id);
            $params['left_times_count']=$category->timescardquota;
            $params['card_type']=$category->cardtype;
            if ($category->cardtype == 1 || $category->cardtype == 4) {
                $params['share_times']=$category->sharetimes;
                $params['already_share_times']= 0;
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
                    $expire_date = date('Y-m-d', strtotime("+$expire_times day", strtotime($params['effective_date'])));
                    break;
                case '2':
                    //周
                    $expire_date = date('Y-m-d', strtotime("+$expire_times week", strtotime($params['effective_date'])));
                    break;
                case '3':
                    //月
                    $expire_date = date('Y-m-d', strtotime("+$expire_times month", strtotime($params['effective_date'])));
                    break;
                case '4':
                    //年
                    $expire_date = date('Y-m-d', strtotime("+$expire_times year", strtotime($params['effective_date'])));
                    break;
            }
            $params['expire_time'] = $expire_date;
            // 插入数据并获取自增ID
            $insertId = $this->model->insertGetId($params);
            //生成会员卡卡号
            $card_no=10000000+$insertId;
            $up_info['card_no'] = $card_no;
            $row = $this->model->get($insertId);
            $result = $row->save($up_info);
            Db::commit();
        } catch (\Exception $e){
            Db::rollback();
            $this->resultError($e->getMessage());
        }
    }

    /**
     * 获取会员卡种类列表
     * @param string $card_type 会员卡种类
     * @return array
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public function getCardTypeList(string $card_type)
    {
        $CardCategory = new CardCategory();
        if ($card_type != '0') {
            $where['brand_id'] = $this->brand_id;
            $where['status'] = 1;
            if (!empty($card_type)) {
                $where['cardtype'] = $card_type;
            }
            $card_list = $CardCategory
                ->where($where)
                ->field(['id as card_category_id', 'cardname', 'cardtype', 'cardtypename','card_avatar','timescardquota','sharetimes','shop_name', 'cardprice', 'expiretimes', 'expiretype'])
                ->order("sort asc")
                ->select();
            $list_01 = array();
            $list_02 = array();
            $list_03 = array();

            foreach ($card_list as $k => $v) {
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
                if ($v->cardtype == 1) {
                    $list_01[] = $v;
                }
                if ($v->cardtype == 2) {
                    $list_02[] = $v;
                }
                if ($v->cardtype == 4) {
                    $list_03[] = $v;
                }
            }
            if (sizeof($list_01) > 0) {
                $result['times_card'] = $list_01;
            }
            if (sizeof($list_02) > 0) {
                $result['term_card'] = $list_02;
            }
            if (sizeof($list_03) > 0) {
                $result['course_card'] = $list_03;
            }
        }
        return $result;
    }


    public function getCardPackageList()
    {
        $where['brand_id'] = $this->brand_id;
        $where['status'] = 1;
        $cardPackage =new CardPackage();
        $package_list =$cardPackage
            ->field(['id as card_package_id', 'package_name', 'package_desc', 'total_price','package_avatar'])
            ->where($where)
            ->order("sort asc")
            ->select();
        return $package_list;
    }

    public function getCardTypeInfo($card_category_id)
    {
        $where['brand_id'] = $this->brand_id;
        $where['status'] = 1;
        $where['id'] = $card_category_id;
        $CardCategory = new CardCategory();
        $card_info = $CardCategory
            ->where($where)
            ->field(['id as card_category_id', 'cardname', 'cardtype', 'cardtypename','card_avatar','card_desc','timescardquota','sharetimes', 'cardprice', 'expiretimes', 'expiretype','shop_name','apply_shop','apply_course','use_limits'])
            ->find();
        if (!$card_info) {
            $this->resultError(__('会员卡类型不存在！'));
        }
        switch ($card_info->expiretype) {
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
        $card_info->expiretimes = "$card_info->expiretimes$time";
        unset($card_info->expiretype);
        if($card_info->cardtype==2){
            unset($card_info->timescardquota);
            unset($card_info->sharetimes);
        }
        return $card_info;
    }

    /**
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws DataNotFoundException
     */
    public function getCardPackageInfo($card_package_id)
    {
        if (empty($card_package_id)) {
            $this->resultError("会员卡套餐ID不能为空");
        }
        $cardPackage = new CardPackage();
        $wh['brand_id'] = $this->brand_id;
        $wh['id'] = $card_package_id;
        $wh['status'] = 1;
        $package_info = $cardPackage
            ->where($wh)
            ->field(['id as card_package_id', 'package_name', 'package_desc', 'total_price','package_avatar'])
            ->find();

        if (empty($package_info)) {
            $this->resultError("会员卡套餐ID不存在");
        }

        $packageDetails = new PackageDetails();
        $where['package_details.status'] = 1;
        $where['card_package_id'] = $card_package_id;
        $details = $packageDetails
            ->field(['id as package_details_id', 'card_package_id', 'card_category_id'])
            ->with(['category' => function($query){
                $query->withField(['cardname', 'cardtype', 'cardtypename', 'card_avatar', 'card_desc', 'cardprice', 'expiretimes', 'expiretype', 'shop_name', 'apply_shop', 'apply_course', 'use_limits']);
            }])
            ->where($where)
            ->select();
        foreach ($details as $k => $v) {
            switch ($v->category['expiretype']) {
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
            $v->category['expiretimes'] = $v->category['expiretimes'] . $time;
        }
        $package_info['card_category_list'] = $details;

        return $package_info;
    }

    public function timesCardShare($user_card_id)
    {
        $params['user_card_id'] = $user_card_id;
        $user_card = $this->model->get($user_card_id);
        if ($user_card->share_times<=0){
            $this->resultError('分享失败-次卡卡种不允许分享！');
        }
        if ($user_card->left_share_times<=0){
            $this->resultError('分享失败-剩余分享次数不足！');
        }
        if($user_card->left_times_count<=0){
            $this->resultError('分享失败-次卡剩余次数不足！');
        }
        $params['card_category_id'] = $user_card->card_category_id;
        $params['shop_id'] = $user_card->shop_id;
        $params['from_user_id'] = $this->getUserId();
        $params['share_time'] = date('Y-m-d H:i:s');
        //增加24小时
        $params['share_expire_time'] = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($params['share_time'])));
        $params['card_effective_date'] = $user_card->effective_date;
        $params['card_expire_time'] = $user_card->expire_time;
        $params['status'] = 0;
        $result = [];
        try {
            Db::startTrans();
            $db = Db::name('xilufitness_times_card_share');
            // 插入数据并获取自增ID
            $insertId = $db->insertGetId($params);
            $result['share_id'] = $insertId;
            //剩余额度-1
            $user_card['left_times_count'] = $user_card['left_times_count'] - 1;
            //分享次数+1
            $user_card['already_share_times'] = $user_card['already_share_times'] + 1;
            //剩余分享次数-1
            $user_card['left_share_times'] = $user_card['left_share_times'] - 1;
            $user_card->save();
            Db::commit();
        } catch (\Exception $e) {
            Log::log($e);
            Db::rollback();
            $this->resultError(__('次卡分享失败'));
        }
        return $result;
    }

    public function userCardVerification($user_card_id,$user_id)
    {
        $user_card = $this->model->get($user_card_id);
        $admin_access = new AdminAccess();
        $admin = $admin_access->where(['brand_id' => $this->brand_id,'admin_id' => $this->getUserId()])->find();
        if (empty($admin)){
            $this->resultError('核销失败-暂无权限核销！');
        }

        if($user_card->effective_date > date('Y-m-d')){
            $this->resultError('核销失败-会员卡未生效！');
        }

        if($user_card->expire_time < date('Y-m-d')){
            $this->resultError('核销失败-会员卡已过期！');
        }

        if($user_card->card_type==1 || $user_card->card_type==4){
            if ($user_card->left_times_count <= 0) {
                $this->resultError('核销失败-会员卡剩余次数不足！');
            }
            //剩余额度-1
            $user_card['left_times_count'] = $user_card['left_times_count'] - 1;
            $user_card['updatetime'] = time();
            $user_card->save();
        }

        //保存核销记录
        $params['brand_id'] = $this->brand_id;
        $params['user_card_id'] = $user_card_id;
        $params['card_category_id'] = $user_card->card_category_id;
        $params['shop_id'] = $admin-> shop_id;
        $params['user_id'] = $user_id;
        $params['admin_user_id'] = $this->getUserId();
        $params['check_time'] = date('Y-m-d H:i:s');
        $params['check_type'] = 1;
        $params['createtime'] = time();
        $params['status'] = 1;
        $records = new CardVerificationRecords();
        $records->save($params);
        $this->resultSuccess('核销成功');
    }

    public function onceCardVerification($share_id,$user_id)
    {
        $admin_access = new AdminAccess();
        $admin = $admin_access->where(['brand_id' => $this->brand_id,'admin_id' => $this->getUserId()])->find();
        if (empty($admin)){
            $this->resultError('核销失败-暂无权限核销！');
        }
        $times_card_share = new TimesCardShare();
        $share_info = $times_card_share->get($share_id);
        if($share_info->status == 3){
            $this->resultError('核销失败-单次卡不可重复使用！');
        }

        if($share_info->card_effective_date > date('Y-m-d')){
            $this->resultError('核销失败-单次卡未生效！');
        }

        if($share_info->card_expire_time < date('Y-m-d')){
            $this->resultError('核销失败-单次卡已过期！');
        }

        if($share_info->status == 1){
            $share['status'] = '3';
            $share['use_time'] = date('Y-m-d H:i:s');
            $share_info->save($share);
            //保存核销记录
            $params['brand_id'] = $this->brand_id;
            $params['user_card_id'] = $share_info -> user_card_id;
            $params['card_category_id'] = $share_info -> card_category_id;
            $params['share_id'] = $share_id;
            $params['shop_id'] = $admin-> shop_id;
            $params['user_id'] = $user_id;
            $params['admin_user_id'] = $this->getUserId();
            $params['check_time'] = date('Y-m-d H:i:s');
            $params['check_type'] = 1;
            $params['createtime'] = time();
            $params['status'] = 1;
            $records = new CardVerificationRecords();
            $records->save($params);
            $this->resultSuccess('核销成功');
        }else{
            $this->resultError('核销失败-单次卡状态无效');
        }
    }


    /**
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getCardVerificationRecords($user_card_id,$page_index,$page_size)
    {
        $user_info = $this->userInfo;
        $mobile = $user_info->getData('mobile');
        $coachModel = new \addons\xilufitness\model\Coach;
        $coachInfo = $coachModel
            ->where(['coach_mobile' => $mobile ])
            ->field(['id','coach_name'])
            ->find();
        if (!empty($coachInfo)) {
            $where['admin_user_id'] = $this->getUserId();
        }else{
            $where['card_verification_records.user_id'] = $this->getUserId();
        }
        if (!empty($user_card_id)) {
            $where['user_card_id'] = $user_card_id;
        }
        $where['card_verification_records.brand_id'] = $this->brand_id;

        $records = new CardVerificationRecords();
        $list= $records
            ->field(['user_card_id','share_id','shop_id','user_id','admin_user_id','check_time','status'])
            ->where($where)
            ->with(['user' => function($query){
                return $query->withField(['nickname','avatar']);
            },'adminUser' => function($query){
                return $query->withField(['nickname','avatar']);
            },'card' => function($query){
                return $query->withField(['card_no', 'card_type']);
            },'shop' => function($query){
                return $query->withField(['shop_name','address']);
            },'category' => function($query){
                return $query->withField(['cardname', 'cardtype', 'cardtypename']);
            }])
            ->order('check_time desc')
            ->paginate($page_size, false, ['page' => $page_index]);
        return $list;
    }

    public function getTimesCardShare($user_card_id)
    {
        $where['user_card_id'] = $user_card_id;
        $times_card_share = new TimesCardShare();

        $list = $times_card_share
            ->field(['id as share_id','user_card_id','from_user_id','to_user_id','share_time','receive_time','share_expire_time','status'])
            ->where(['user_card_id' => $user_card_id])
            ->select();
        return $list;
    }

    public function receiveTimesCardShare($share_id)
    {
        $times_card_share = new TimesCardShare();
        $share = $times_card_share->get($share_id);
        if($share->status == 2){
            $this->resultError('领取失败-次卡分享已失效！');
        }

        if(date('Y-m-d H:i:s') > $share->share_expire_time){
            $this->resultError('领取失败-次卡分享已失效！');
        }

        if($share->status == 1){
            $this->resultError('领取失败-不允许重复领取！');
        }

        if($share->from_user_id == $this->getUserId()){
            $this->resultError('领取失败-不允许领取自己的分享！');
        }

        if($share->status == 0){
            $params['to_user_id'] = $this->getUserId();
            $params['receive_time'] = date('Y-m-d H:i:s');
            $params['status'] = 1;
            $share->save($params);
            $this->resultSuccess('领取成功');
        } else {
            $this->resultError('领取失败');
        }


    }
}