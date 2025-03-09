define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/shop/admin/index',
                    add_url: 'xilufitness/shop/admin/add',
                    edit_url: 'xilufitness/shop/admin/edit',
                    del_url: 'xilufitness/shop/admin/del',
                    multi_url: 'xilufitness/shop/admin/multi',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'state', checkbox: true, },
                        {field: 'id', title: 'ID', searchable:false},
                        {field: 'admin.username', title: __('Username'),operate: 'LIKE'},
                        {field: 'admin.nickname', title: __('Nickname'),operate: 'LIKE'},
                        {field: 'admin.groups_text', title: __('Group'), operate:false, formatter: Table.api.formatter.label},
                        {field: 'admin.mobile', title: __('Mobile'),operate: 'LIKE'},
                        {field: 'admin.status', title: __("Status"), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'admin.logintime', title: __('Login time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'brand.brand_name',title:__('Brand_name'),operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: function (value, row, index) {
                                return Table.api.formatter.operate.call(this, value, row, index);
                            }}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $("#shop_id").data("params",function () {
                return {custom:{brand_id:$("#brand_id").val(),status:'normal'}};
            });
            $("#brand_id").on("change",function () {
                $("#shop_id").data("params",function () {
                    return {custom:{brand_id:$("#brand_id").val(),status:'normal'}};
                });
            });
            Form.api.bindevent($("form[role=form]"));
        },
        edit: function () {
            $("#shop_id").data("params",function () {
                return {custom:{brand_id:$("#brand_id").val(),status:'normal'}};
            });
            $("#brand_id").on("change",function () {
                $("#shop_id").data("params",function () {
                    return {custom:{brand_id:$("#brand_id").val(),status:'normal'}};
                });
            });
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});
