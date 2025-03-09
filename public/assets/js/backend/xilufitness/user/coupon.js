define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/user/coupon/index' + location.search,
                    edit_url: 'xilufitness/user/coupon/edit',
                    del_url: 'xilufitness/user/coupon/del',
                    table: 'xilufitness_user_coupon',
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
                    if(Config.coupon_id > 0){
                        params.filter.coupon_id = Config.coupon_id;
                    }
                    if(Config.user_id > 0){
                        params.filter.user_id = Config.user_id;
                    }
                    params.filter = JSON.stringify(params.filter);
                    return params;
                },
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: 'BETWEEN', sortable:true},
                        {field: 'user.nickname', title: __('User_id'),operate: 'LIKE'},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'meet_amount', title: __('Meet_amount'), operate:'BETWEEN',formatter: function (value,row) {
                                return '¥' + (row.meet_amount || 0) ;
                            }},
                        {field: 'discount_amount', title: __('Discount_amount'), operate:'BETWEEN',formatter: function (value,row) {
                                return '¥' + (row.discount_amount || 0) ;
                            }},
                        {field: 'expire_time', title: __('Expire_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'coupon_status', title: __('Coupon_status'),searchList:Config.statusList,formatter:Table.api.formatter.status},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
