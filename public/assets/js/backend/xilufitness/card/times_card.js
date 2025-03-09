define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/card/times_card/index',
                    add_url: 'xilufitness/card/times_card/add',
                    edit_url: 'xilufitness/card/times_card/edit',
                    del_url: 'xilufitness/card/times_card/del',
                    multi_url: 'xilufitness/card/times_card/multi',
                    table: 'category',
                }
            });

            var table = $("#card-table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('编号'), sortable: true, operate: false},
                        {field: 'cardname', title: __('会员卡名称') , operate: 'LIKE'},
                        {field: 'cardtypename', title: __('会员卡类型') , operate: false},
                        {field: 'timescardquota', title: __('次卡额度（次）') },
                        {field: 'cardprice', title: __('售价（元）'),operate: 'BETWEEN'},
                        {field: 'sharetimes', title: __('可分享次数'),operate: 'LIKE'},
                        {field: 'expiretimes', title: __('有效期'), operate: false},
                        {field: 'brand.brand_name', title: __('所属小程序'), operate: 'LIKE'},
                        {field: 'sort', title: __('展示顺序'), operate: false},
                        {field: 'status', title: __('是否启用'), searchList: {"1":__('启用'),"0":__('禁用')},formatter: Table.api.formatter.status},
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