define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/user/point_rule/index' + location.search,
                    add_url: 'xilufitness/user/point_rule/add',
                    edit_url: 'xilufitness/user/point_rule/edit',
                    del_url: 'xilufitness/user/point_rule/del',
                    multi_url: 'xilufitness/user/point_rule/multi',
                    import_url: 'xilufitness/user/point_rule/import',
                    table: 'xilufitness_point_rule',
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
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'point_amount', title: __('Point_amount'), operate: 'BETWEEN'},
                        {field: 'point_ratio', title: __('Point_ratio'), operate:'BETWEEN',formatter: function (value,row,index) {
                                return row.point_ratio + '%';
                            }, operate: 'BETWEEN'},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $("[name='row[point_type]']").on("change",function () {
                let rule_type = $(this).val();
                if(rule_type == 2){
                    $("#c-point_ratio").parent().parent().parent().show();
                    $("#c-point_amount").parent().parent().hide();
                } else {
                    $("#c-point_ratio").parent().parent().parent().hide();
                    $("#c-point_amount").parent().parent().show();
                }
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $("[name='row[point_type]']").on("change",function () {
                let rule_type = $(this).val();
                if(rule_type == 2){
                    $("#c-point_ratio").parent().parent().parent().show();
                    $("#c-point_amount").parent().parent().hide();
                } else {
                    $("#c-point_ratio").parent().parent().parent().hide();
                    $("#c-point_amount").parent().parent().show();
                }
            });
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
