<?php

namespace addons\xilufitness;

use addons\xilufitness\library\Hook;
use app\common\library\Menu;
use think\Addons;
use think\Db;
use think\Lang;

/**
 * 插件
 */
class Xilufitness extends Addons
{
    /**
     * 菜单配置
     */
    protected $menu = [
        [
            'name'    => 'xilufitness',
            'title'   => '聚岩系统',
            'icon'    => 'fa fa-slideshare',
            'ismenu'  => 1,
            'weigh'   => 1,
            'remark'  => '多商户小程序管理系统',
            'sublist' => [
                [
                    'name'    => 'xilufitness/analyse',
                    'title'   => '控制台',
                    'icon'    => 'fa fa-windows',
                    'ismenu'  => 1,
                    'weigh'   => 38,
                    'sublist' => [
                        [
                            'name'    => 'xilufitness/analyse/index',
                            'title'   => '数据分析',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 39,
                            'sublist' => [
                                ['name' => 'xilufitness/analyse/index/index', 'title' => '查看'],
                                ['name' => 'xilufitness/analyse/index/get_data', 'title' => '订单统计']
                            ]
                        ]
                    ]
                ],
                [
                    'name'    => 'xilufitness/brand',
                    'title'   => '小程序管理',
                    'icon'    => 'fa fa-sitemap',
                    'ismenu'  => 1,
                    'weigh'   => 37,
                    'sublist' => [
                        [
                           'name'    => 'xilufitness/brand/index/config',
                           'title'   => '小程序配置',
                           'icon'    => 'fa fa-circle-o',
                           'ismenu'  => 1,
                           'weigh'   => 4
                       ],
                        ['name' => 'xilufitness/brand/index/index', 'title' => '查看'],
                    ]
                ],
                [
                    'name'    => 'xilufitness/shop',
                    'title'   => '门店管理',
                    'icon'    => 'fa fa-delicious',
                    'ismenu'  => 1,
                    'weigh'   => 28,
                    'sublist' => [
                        [
                            'name'    => 'xilufitness/shop/index',
                            'title'   => '门店列表',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 8,
                            'sublist' => [
                                ['name' => 'xilufitness/shop/index/index', 'title' => '查看'],
                                ['name' => 'xilufitness/shop/index/add', 'title' => '添加'],
                                ['name' => 'xilufitness/shop/index/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/shop/index/del', 'title' => '删除'],
                                ['name' => 'xilufitness/shop/index/recyclebin', 'title' => '回收站'],
                                ['name' => 'xilufitness/shop/index/destroy', 'title' => '真实删除'],
                                ['name' => 'xilufitness/shop/index/restore', 'title' => '还原'],
                                ['name' => 'xilufitness/shop/index/multi', 'title' => '批量更新'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/shop/admin',
                            'title'   => '门店账号管理',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 6,
                            'sublist' => [
                                ['name' => 'xilufitness/shop/admin/index', 'title' => '查看'],
                                ['name' => 'xilufitness/shop/admin/add', 'title' => '添加'],
                                ['name' => 'xilufitness/shop/admin/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/shop/admin/del', 'title' => '删除']
                            ]
                        ]
                    ]
                ],
                [
                    'name'    => 'xilufitness/lable',
                    'title'   => '标签管理',
                    'icon'    => 'fa fa-chain',
                    'ismenu'  => 1,
                    'weigh'   => 21,
                    'sublist' => [
                       [
                           'name'    => 'xilufitness/lable/index',
                           'title'   => '标签列表',
                           'icon'    => 'fa fa-circle-o',
                           'ismenu'  => 1,
                           'weigh'   => 10,
                           'sublist' => [
                               ['name' => 'xilufitness/lable/index/index', 'title' => '查看'],
                               ['name' => 'xilufitness/lable/index/add', 'title' => '添加'],
                               ['name' => 'xilufitness/lable/index/edit', 'title' => '编辑'],
                               ['name' => 'xilufitness/lable/index/del', 'title' => '删除'],
                               ['name' => 'xilufitness/lable/index/multi', 'title' => '批量更新'],
                           ]
                       ]
                    ]
                ],
                [
                    'name'    => 'xilufitness/coach',
                    'title'   => '教练管理',
                    'icon'    => 'fa fa-street-view',
                    'ismenu'  => 1,
                    'weigh'   => 30,
                    'sublist' => [
                        [
                            'name'    => 'xilufitness/coach/index',
                            'title'   => '教练管理',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 17,
                            'sublist' => [
                                ['name' => 'xilufitness/coach/index/index', 'title' => '查看'],
                                ['name' => 'xilufitness/coach/index/add', 'title' => '添加'],
                                ['name' => 'xilufitness/coach/index/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/coach/index/del', 'title' => '删除'],
                                ['name' => 'xilufitness/coach/index/multi', 'title' => '批量更新'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/coach/group',
                            'title'   => '教练等级管理',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 16,
                            'sublist' => [
                                ['name' => 'xilufitness/coach/group/index', 'title' => '查看'],
                                ['name' => 'xilufitness/coach/group/add', 'title' => '添加'],
                                ['name' => 'xilufitness/coach/group/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/coach/group/del', 'title' => '删除'],
                                ['name' => 'xilufitness/coach/group/multi', 'title' => '批量更新'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/coach/cash',
                            'title'   => '教练收入流水',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 15,
                            'sublist' => [
                                ['name' => 'xilufitness/coach/cash/index', 'title' => '查看'],
                                ['name' => 'xilufitness/coach/cash/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/coach/cash/del', 'title' => '删除'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/coach/account',
                            'title'   => '教练基本信息',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 14,
                            'sublist' => [
                                ['name' => 'xilufitness/coach/account/index', 'title' => '查看'],
                                ['name' => 'xilufitness/coach/account/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/coach/account/del', 'title' => '删除'],
                            ]
                        ],

                        [
                            'name'    => 'xilufitness/coach/withdraw',
                            'title'   => '教练提现记录',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 12,
                            'sublist' => [
                                ['name' => 'xilufitness/coach/withdraw/index', 'title' => '查看'],
                                ['name' => 'xilufitness/coach/withdraw/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/coach/withdraw/del', 'title' => '删除'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/coach/report',
                            'title'   => '请假报备',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 13,
                            'sublist' => [
                                ['name' => 'xilufitness/coach/report/index', 'title' => '查看'],
                                ['name' => 'xilufitness/coach/report/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/coach/report/del', 'title' => '删除'],
                            ]
                        ]
                    ]
                ],
                [
                    'name'    => 'xilufitness/course',
                    'title'   => '课程/活动/管理',
                    'icon'    => 'fa fa-clone',
                    'ismenu'  => 1,
                    'weigh'   => 24,
                    'sublist' => [
                        [
                            'name'    => 'xilufitness/course/camp',
                            'title'   => '活动管理',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 18,
                            'sublist' => [
                                ['name' => 'xilufitness/course/camp/index', 'title' => '查看'],
                                ['name' => 'xilufitness/course/camp/add', 'title' => '添加'],
                                ['name' => 'xilufitness/course/camp/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/course/camp/del', 'title' => '删除'],
                                ['name' => 'xilufitness/course/camp/multi', 'title' => '批量更新']
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/course/index',
                            'title'   => '课程管理',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 19,
                            'sublist' => [
                                ['name' => 'xilufitness/course/index/index', 'title' => '查看'],
                                ['name' => 'xilufitness/course/index/add', 'title' => '添加'],
                                ['name' => 'xilufitness/course/index/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/course/index/del', 'title' => '删除'],
                                ['name' => 'xilufitness/course/index/multi', 'title' => '批量更新']
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/course/cate',
                            'title'   => '课程分类',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 20,
                            'sublist' => [
                                ['name' => 'xilufitness/course/cate/index', 'title' => '查看'],
                                ['name' => 'xilufitness/course/cate/add', 'title' => '添加'],
                                ['name' => 'xilufitness/course/cate/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/course/cate/del', 'title' => '删除'],
                                ['name' => 'xilufitness/course/cate/multi', 'title' => '批量更新']
                            ]
                        ]
                    ]
                ],
                [
                    'name'    => 'xilufitness/work',
                    'title'   => '排课管理',
                    'icon'    => 'fa fa-calendar',
                    'ismenu'  => 1,
                    'weigh'   => 18,
                    'sublist' => [
                        [
                            'name'     => 'xilufitness/work/camp',
                            'title'    => '活动排课',
                            'icon'     => 'fa fa-circle-o',
                            'ismenu'   => 1,
                            'weigh'    => 22,
                            'sublist'  => [
                                ['name' => 'xilufitness/work/camp/index', 'title' => '查看'],
                                ['name' => 'xilufitness/work/camp/add', 'title' => '添加'],
                                ['name' => 'xilufitness/work/camp/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/work/camp/del', 'title' => '删除'],
                                ['name' => 'xilufitness/work/camp/multi', 'title' => '批量更新'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/work/course',
                            'title'   => '课程排课',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 23,
                            'sublist' => [
                                ['name' => 'xilufitness/work/course/index', 'title' => '查看'],
                                ['name' => 'xilufitness/work/course/add', 'title' => '添加'],
                                ['name' => 'xilufitness/work/course/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/work/course/del', 'title' => '删除'],
                                ['name' => 'xilufitness/work/course/multi', 'title' => '批量更新'],
                            ]
                        ],
                    ]
                ],
                [
                    'name'    => 'xilufitness/activity',
                    'title'   => '营销活动管理',
                    'icon'    => 'fa fa-cc-visa',
                    'ismenu'  => 1,
                    'weigh'   => 9,
                    'sublist' => [
                        [
                            'name'    => 'xilufitness/activity/medal',
                            'title'   => '勋章管理',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 25,
                            'sublist' => [
                                ['name' => 'xilufitness/activity/medal/index', 'title' => '查看'],
                                ['name' => 'xilufitness/activity/medal/add', 'title' => '添加'],
                                ['name' => 'xilufitness/activity/medal/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/activity/medal/del', 'title' => '删除'],
                                ['name' => 'xilufitness/activity/medal/multi', 'title' => '批量更新'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/activity/coupon',
                            'title'   => '代金券管理',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 26,
                            'sublist' => [
                                ['name' => 'xilufitness/activity/coupon/index', 'title' => '查看'],
                                ['name' => 'xilufitness/activity/coupon/add', 'title' => '添加'],
                                ['name' => 'xilufitness/activity/coupon/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/activity/coupon/del', 'title' => '删除'],
                                ['name' => 'xilufitness/activity/coupon/multi', 'title' => '批量更新'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/activity/recharge',
                            'title'   => '充值设置',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 27,
                            'sublist' => [
                                ['name' => 'xilufitness/activity/recharge/index', 'title' => '查看'],
                                ['name' => 'xilufitness/activity/recharge/add', 'title' => '添加'],
                                ['name' => 'xilufitness/activity/recharge/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/activity/recharge/del', 'title' => '删除'],
                                ['name' => 'xilufitness/activity/recharge/multi', 'title' => '批量更新'],
                            ]
                        ]
                    ]
                ],
                [
                    'name'    => 'xilufitness/banner',
                    'title'   => '图片轮播管理',
                    'icon'    => 'fa fa-file-picture-o',
                    'ismenu'  => 1,
                    'weigh'   => 5,
                    'sublist' => [
                        [
                            'name'    => 'xilufitness/banner/index',
                            'title'   => '轮播图片列表',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 29,
                            'sublist' => [
                                ['name' => 'xilufitness/banner/index/index', 'title' => '查看'],
                                ['name' => 'xilufitness/banner/index/add', 'title' => '添加'],
                                ['name' => 'xilufitness/banner/index/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/banner/index/del', 'title' => '删除'],
                                ['name' => 'xilufitness/banner/index/multi', 'title' => '批量更新'],
                            ]
                        ],
                    ]
                ],
                [
                    'name'    => 'xilufitness/order',
                    'title'   => '订单管理',
                    'icon'    => 'fa fa-database',
                    'ismenu'  => 1,
                    'weigh'   => 11,
                    'sublist' => [
                        [
                            'name'    => 'xilufitness/order/camp',
                            'title'   => '活动订单',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 31,
                            'sublist' => [
                                ['name' => 'xilufitness/order/camp/index', 'title' => '查看'],
                                ['name' => 'xilufitness/order/camp/add', 'title' => '添加'],
                                ['name' => 'xilufitness/order/camp/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/order/camp/del', 'title' => '删除'],
                                ['name' => 'xilufitness/order/camp/recyclebin', 'title' => '回收站'],
                                ['name' => 'xilufitness/order/camp/destroy', 'title' => '真实删除'],
                                ['name' => 'xilufitness/order/camp/restore', 'title' => '还原']
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/order/personal',
                            'title'   => '私教订单',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 32,
                            'sublist' => [
                                ['name' => 'xilufitness/order/personal/index', 'title' => '查看'],
                                ['name' => 'xilufitness/order/personal/add', 'title' => '添加'],
                                ['name' => 'xilufitness/order/personal/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/order/personal/del', 'title' => '删除'],
                                ['name' => 'xilufitness/order/personal/recyclebin', 'title' => '回收站'],
                                ['name' => 'xilufitness/order/personal/destroy', 'title' => '真实删除'],
                                ['name' => 'xilufitness/order/personal/restore', 'title' => '还原']
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/order/course',
                            'title'   => '团课订单',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 33,
                            'sublist' => [
                                ['name' => 'xilufitness/order/course/index', 'title' => '查看'],
                                ['name' => 'xilufitness/order/course/add', 'title' => '添加'],
                                ['name' => 'xilufitness/order/course/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/order/course/del', 'title' => '删除'],
                                ['name' => 'xilufitness/order/course/recyclebin', 'title' => '回收站'],
                                ['name' => 'xilufitness/order/course/destroy', 'title' => '真实删除'],
                                ['name' => 'xilufitness/order/course/restore', 'title' => '还原']
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/order/index',
                            'title'   => '会员充值订单',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 34,
                            'sublist' => [
                                ['name' => 'xilufitness/order/index/index', 'title' => '查看'],
                                ['name' => 'xilufitness/order/index/add', 'title' => '添加'],
                                ['name' => 'xilufitness/order/index/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/order/index/del', 'title' => '删除'],
                                ['name' => 'xilufitness/order/index/recyclebin', 'title' => '回收站'],
                                ['name' => 'xilufitness/order/index/destroy', 'title' => '真实删除'],
                                ['name' => 'xilufitness/order/index/restore', 'title' => '还原']
                            ]
                        ]
                    ]
                ],
                [
                    'name'    => 'xilufitness/cms',
                    'title'   => '单页管理',
                    'icon'    => 'fa fa-file-code-o',
                    'ismenu'  => 1,
                    'weigh'   => 2,
                    'sublist' => [
                        [
                            'name'    => 'xilufitness/cms/index',
                            'title'   => '单页列表',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 36,
                            'sublist' => [
                                ['name' => 'xilufitness/cms/index/index', 'title' => '查看'],
                                ['name' => 'xilufitness/cms/index/add', 'title' => '添加'],
                                ['name' => 'xilufitness/cms/index/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/cms/index/del', 'title' => '删除'],
                                ['name' => 'xilufitness/cms/index/multi', 'title' => '批量更新'],
                            ]
                        ]
                    ]
                ],
                [
                    'name'    => 'xilufitness/user',
                    'title'   => '会员管理',
                    'icon'    => 'fa fa-address-card',
                    'ismenu'  => 1,
                    'weigh'   => 35,
                    'sublist' => [
                        [
                            'name'    => 'xilufitness/user/media',
                            'title'   => '解锁勋章记录',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 38,
                            'sublist' => [
                                ['name' => 'xilufitness/user/media/index', 'title' => '查看'],
                                ['name' => 'xilufitness/user/media/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/user/media/del', 'title' => '删除'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/user/coupon',
                            'title'   => '代金券记录',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 39,
                            'sublist' => [
                                ['name' => 'xilufitness/user/coupon/index', 'title' => '查看'],
                                ['name' => 'xilufitness/user/coupon/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/user/coupon/del', 'title' => '删除'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/user/comment',
                            'title'   => '评论管理',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 40,
                            'sublist' => [
                                ['name' => 'xilufitness/user/comment/index', 'title' => '查看'],
                                ['name' => 'xilufitness/user/comment/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/user/comment/del', 'title' => '删除'],
                                ['name' => 'xilufitness/user/comment/multi', 'title' => '批量更新'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/user/collect',
                            'title'   => '收藏记录',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 41,
                            'sublist' => [
                                ['name' => 'xilufitness/user/collect/index', 'title' => '查看'],
                                ['name' => 'xilufitness/user/collect/del', 'title' => '删除'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/user/user_point',
                            'title'   => '积分明细',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 42,
                            'sublist' => [
                                ['name' => 'xilufitness/user/user_point/index', 'title' => '查看'],
                                ['name' => 'xilufitness/user/user_point/add', 'title' => '添加'],
                                ['name' => 'xilufitness/user/user_point/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/user/user_point/del', 'title' => '删除'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/user/account',
                            'title'   => '余额明细',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 44,
                            'sublist' => [
                                ['name' => 'xilufitness/user/account/index', 'title' => '查看'],
                                ['name' => 'xilufitness/user/account/add', 'title' => '添加'],
                                ['name' => 'xilufitness/user/account/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/user/account/del', 'title' => '删除'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/user/point_rule',
                            'title'   => '积分规则',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 43,
                            'sublist' => [
                                ['name' => 'xilufitness/user/point_rule/index', 'title' => '查看'],
                                ['name' => 'xilufitness/user/point_rule/add', 'title' => '添加'],
                                ['name' => 'xilufitness/user/point_rule/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/user/point_rule/del', 'title' => '删除'],
                                ['name' => 'xilufitness/user/point_rule/multi', 'title' => '批量更新'],
                            ]
                        ],
                        [
                            'name'    => 'xilufitness/user/index',
                            'title'   => '会员列表管理',
                            'icon'    => 'fa fa-circle-o',
                            'ismenu'  => 1,
                            'weigh'   => 45,
                            'sublist' => [
                                ['name' => 'xilufitness/user/index/index', 'title' => '查看'],
                                ['name' => 'xilufitness/user/index/edit', 'title' => '编辑'],
                                ['name' => 'xilufitness/user/index/del', 'title' => '删除'],
                                ['name' => 'xilufitness/user/index/multi', 'title' => '批量更新'],
                            ]
                        ],
                    ]
                ]
            ]
        ]
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        Menu::create($this->menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete("xilufitness");
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable("xilufitness");
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable("xilufitness");
        return true;
    }

    /**
     * 插件升级方法
     * @return bool
     */
    public function upgrade()
    {
        //如果菜单有变更则升级菜单
        Menu::upgrade('xilufitness', $this->menu);
        return true;
    }

    /**
     * 应用初始化
     */
    public function appInit()
    {
        // 公共方法
        require_once __DIR__ . '/helper/helper.php';
        if(!class_exists('\QRcode')){
            require_once __DIR__ .'/library/phpqrcode.php';
        }
        //全局事件注册
        Hook::register();
        //加载语言包
        Lang::load(glob(APP_PATH. '/api/lang/zh-cn/*/*'));
        //加载第三方类库
        if(!class_exists('\WeChat\Pay')){
            \think\Loader::addNamespace('WeChat',ADDON_PATH . 'xilufitness' . DS . 'library' . DS . 'zoujingli' . DS . 'wechat-developer' . DS . 'WeChat' . DS);
        }
        if(!class_exists('WeMini\Crypt')){
            \think\Loader::addNamespace('WeMini',ADDON_PATH . 'xilufitness' . DS . 'library' . DS . 'zoujingli' . DS . 'wechat-developer' . DS . 'WeMini' . DS);
        }
        if(!class_exists('WePay\Order')){
            \think\Loader::addNamespace('WePay',ADDON_PATH . 'xilufitness' . DS . 'library' . DS . 'zoujingli' . DS . 'wechat-developer' . DS . 'WePay' . DS);
        }
        if(!class_exists('WePayV3\Order')){
            \think\Loader::addNamespace('WePayV3',ADDON_PATH . 'xilufitness' . DS . 'library' . DS . 'zoujingli' . DS . 'wechat-developer' . DS . 'WePayV3' . DS);
        }
    }



}
