<?php


namespace addons\xilufitness\listener;


use addons\xilufitness\library\Aescbc;

class User
{

    /**
     * 用户注册登录成功事件
     */
    public function xilufitnessUserLoginSuccess($params){
        if(empty($params['user_id']) || empty($params['openid']) ||
            empty($params['brand_id'])) {
            return false;
        }
        $params['user_id'] = xilufitness_get_id_value($params['user_id']);
        $authModel = new \addons\xilufitness\model\UserConnect;
        $connectInfo = $authModel
            ->where(['account_user_id' => $params['user_id'], 'brand_id' => $params['brand_id']])
            ->find();
        if(empty($connectInfo)){
            $result = $authModel->allowField(true)->save([
                'account_user_id' => $params['user_id'],
                'openid' => $params['openid'],
                'brand_id' => $params['brand_id']
            ]);
            return $result;
        } else {
            $connectInfo->allowField(true)->save(['openid' => $params['openid']]);
        }
        return false;
    }

    /**
     * 余额变动监听
     */
    public function xilufitnessUserAccountChange($params){
        if(!empty($params) && $params['after_account'] > 0){
            $model = new \addons\xilufitness\model\UserAccount;
            $accountRecord = $model
                ->where(['user_id' => $params['user_id'],'brand_id' => $params['brand_id'],
                    'data_id' => $params['data_id'], 'account_type' => $params['account_type'],
                    'amount_type' => $params['amount_type']])
                ->field(['id'])
                ->find();
           return (empty($accountRecord) && $model->allowField(true)->save($params));
        }
        return false;
    }

    /**
     * 积分变动监听
     */
    public function xilufitnessUserPointChange($params){
        if(!empty($params) && $params['after_point'] > 0){
            $model = new \addons\xilufitness\model\UserPoint;
            $pointRecord = $model
                ->where(['user_id' => $params['user_id'],'brand_id' => $params['brand_id'],
                    'data_id' => $params['data_id'], 'rule_type' => $params['rule_type'],
                    'point_type' => $params['point_type']])
                ->field(['id'])
                ->find();
            return (empty($pointRecord) && $model->allowField(true)->save($params));
        }
        return false;
    }

    /**
     * 勋章解锁
     * @param array $params
     */
    public function xilufitnessMedalUnlocking($params){
        $model = new \addons\xilufitness\model\User;
        $userMediaModel = new \addons\xilufitness\model\UserMedia;
        $mediaModel = new \addons\xilufitness\model\ActivityMedia;
        $userInfo = $model
            ->where(['id' => $params['user_id'] ?? 0, 'brand_id' => $params['brand_id']])
            ->find();
        if(!empty($userInfo)){
            $user_media_list = $userMediaModel
                ->where(['user_id' => $params['user_id'] ?? 0, 'brand_id' => $params['brand_id'] ?? 0])
                ->field(['media_id','train_duration'])
                ->select();
            $train_duration = $userInfo->getData('train_duration') ?? 0;
            $useMediaDuration = array_sum(array_column($user_media_list,'train_duration'));
            $left_duration = $train_duration - $useMediaDuration;
            if(!empty($left_duration) && $left_duration > 0){
                $media_ids = array_column($user_media_list,'media_id') ?? [-1];
                $mediaInfo = $mediaModel
                    ->normal()
                    ->where(['brand_id' => $params['brand_id'], 'class_time' => ['elt',$left_duration], 'id' => ['notin',$media_ids] ])
                    ->field(['id','class_time'])
                    ->order('class_time asc')
                    ->find();
                if(!empty($mediaInfo) && $mediaInfo['class_time'] <= $left_duration){
                   return $userMediaModel->allowField(true)->save([
                        'user_id' => $params['user_id'],
                        'brand_id' => $params['brand_id'],
                        'media_id' => xilufitness_get_id_value($mediaInfo['id']),
                        'train_duration' => $mediaInfo['class_time']
                    ]);
                }

            }

        }
        return  false;
    }

    /**
     * 用户推荐
     * 邀请有礼记录
     */
    public function xilufitnessUserShare($params){
        if(!empty($params['user_id']) && !empty($params['rec_user_id']) && !empty($params['brand_id'])){
            $model = new \addons\xilufitness\model\UserShareRecord;
            $exist = $model
                ->where(['user_id' => $params['user_id'], 'brand_id' => $params['brand_id'], 'rec_user_id' => $params['rec_user_id']])
                ->field(['id'])
                ->find();
            if(empty($exist)){
                return $model->allowField(true)->save($params);
            }
            return false;
        }
        return false;
    }

}