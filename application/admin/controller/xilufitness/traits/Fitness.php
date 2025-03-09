<?php


namespace app\admin\controller\xilufitness\traits;


use app\admin\model\xilufitness\brand\AdminAccess;

trait Fitness
{
    /**
     * 根据登录账号
     * 获取品牌商信息
     * @return int $brand_id
     */
    protected function getFitnessBrandId(): int
    {
        $adminInfo = session('admin');
        $model = new AdminAccess;
        $brand_id = $model->where('admin_id','eq',$adminInfo['id'] ?? 0)->value('brand_id');
        return $brand_id ?? 0;
    }

    /**
     * 根据登录账号
     * 获取门店信息
     * @return int $shop_id
     */
    protected function getFitnessShopId(): int
    {
        $adminInfo = session('admin');
        $model = new AdminAccess;
        $shop_id = $model->where('admin_id','eq',$adminInfo['id'] ?? 0)->value('shop_id');
        return $shop_id ?? 0;
    }

    /**
     * 根据登录账号
     * 判断角色 是品牌商 还是门店
     * @return int $account_type 1 品牌商 2 门店
     */
    protected function getFitnessAccountRole()
    {
        $adminInfo = session('admin');
        $model = new AdminAccess;
        $account_type = $model->where('admin_id','eq',$adminInfo['id'] ?? 0)->value('account_type');
        return $account_type;
    }

}