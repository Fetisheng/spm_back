define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/coach/account/index' + location.search,
                    edit_url: 'xilufitness/coach/account/edit',
                    del_url: 'xilufitness/coach/account/del',
                    multi_url: 'xilufitness/coach/account/multi',
                    import_url: 'xilufitness/coach/account/import',
                    table: 'xilufitness_coach_account',
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
                    if(Config.coach_id > 0){
                        params.filter.coach_id = Config.coach_id;
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
                        {field: 'id', title: __('Id'), searchable:false, sortable:true},
                        {field: 'coach.coach_name', title: __('Coach_id'), operate: 'LIKE'},
                        {field: 'total_account', title: __('Total_account'), operate:'BETWEEN', sortable:true},
                        {field: 'account', title: __('Account'), operate:'BETWEEN', sortable:true},
                        {field: 'withdraw_account', title: __('Withdraw_account'), operate:'BETWEEN', sortable:true},
                        {field: 'course_count', title: __('Course_count'), sortable:true},
                        {field: 'class_duration', title: __('Class_duration'), sortable:true,formatter: function (value,row) {
                                return value + __('Minute');
                            }},
                        {field: 'course_total_count', title: __('Course_total_count'), sortable:true},
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
