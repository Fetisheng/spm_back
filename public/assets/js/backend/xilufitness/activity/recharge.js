define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/activity/recharge/index' + location.search,
                    add_url: 'xilufitness/activity/recharge/add',
                    edit_url: 'xilufitness/activity/recharge/edit',
                    del_url: 'xilufitness/activity/recharge/del',
                    multi_url: 'xilufitness/activity/recharge/multi',
                    import_url: 'xilufitness/activity/recharge/import',
                    table: 'xilufitness_activity_recharge',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: 'BETWEEN', sortable:true},
                        {field: 'recharge_amount', title: __('Recharge_amount'), operate:'BETWEEN', sortable:true},
                        {field: 'account_amount', title: __('Account_amount'), operate:'BETWEEN', sortable:true},
                        {field: 'cut_amount', title: __('Cut_amount'), operate:'BETWEEN', sortable:true},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime, sortable:true},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'order',
                                    text: __('充值列表'),
                                    title: __('充值列表'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row) {
                                        return Config.moduleurl + '/xilufitness/order/index?data_id='+row.id+'&order_type=0'+'&brand_id='+row.brand_id;
                                    },
                                }
                            ],
                            formatter: Table.api.formatter.operate}
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
