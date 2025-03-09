define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/order/course/index' + location.search,
                    edit_url: 'xilufitness/order/course/edit',
                    del_url: 'xilufitness/order/course/del',
                    table: 'xilufitness_order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                queryParams:function(params){
                    params.filter = JSON.parse(params.filter);
                    if(Config.data_id > 0){
                        params.filter.data_id = Config.data_id;
                    }
                    if(Config.shop_id > 0){
                        params.filter.shop_id = Config.shop_id;
                    }
                    if(Config.brand_id > 0){
                        params.filter.brand_id = Config.brand_id;
                    }
                    params.filter = JSON.stringify(params.filter);
                    return params;
                },
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),searchable:false,sortable:true},
                        {field: 'user.nickname', title: __('User_id'), operate: 'LIKE'},
                        {field: 'user.mobile', title: __('User_mobile'), operate: 'LIKE'},
                        {field: 'order_no', title: __('Order_no'), operate: 'LIKE'},
                        {field: 'coupon_amount', title: __('Coupon_amount'), operate:'BETWEEN'},
                        {field: 'pay_amount', title: __('Pay_amount'), operate:'BETWEEN'},
                        {field: 'code_num', title: __('Code_num'), operate:'BETWEEN'},
                        {field: 'pay_status', title: __('Pay_status'),searchList: {0:__('pay_status_0'),1:__('pay_status_1')}, formatter: Table.api.formatter.normal},
                        {field: 'order_status', title: __('Order_status'),searchList: {0:__('Order_status_0'),1:__('Order_status_1'),2:__('Order_status_2'),3:__('Order_status_3'),4:__('Order_status_4'),5:__('Order_status_5'),6:__('Order_status_6'),10:__('Order_status_10')}, formatter: Table.api.formatter.normal},
                        {field: 'pay_type', title: __('Pay_type'),searchList:{0:__('free_pay'),1:__('card_pay'),2:__('wechat_pay')}, formatter: Table.api.formatter.status},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime,sortable:true},
                        {field: 'coach.coach_name', title: __('Coach_id'), operate: 'LIKE'},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'shop.shop_name', title: __('Shop_id'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime,sortable:true},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'xilufitness/order/course/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '140px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'xilufitness/order/course/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'xilufitness/order/course/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
