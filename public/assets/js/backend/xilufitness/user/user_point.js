define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/user/user_point/index' + location.search,
                    add_url: 'xilufitness/user/user_point/add',
                    edit_url: 'xilufitness/user/user_point/edit',
                    del_url: 'xilufitness/user/user_point/del',
                    multi_url: 'xilufitness/user/user_point/multi',
                    import_url: 'xilufitness/user/user_point/import',
                    table: 'xilufitness_user_point',
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
                    if(Config.user_id > 0){
                        params.filter.user_id = Config.user_id;
                    }
                    params.filter = JSON.stringify(params.filter);
                    return params;
                },
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:'BETWEEN',sortable:true},
                        {field: 'user.nickname', title: __('User_id'),operate: 'LIKE'},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'before_point', title: __('Before_point'),operate:'BETWEEN'},
                        {field: 'point', title: __('Point'),operate:'BETWEEN'},
                        {field: 'after_point', title: __('After_point'),operate:'BETWEEN'},
                        {field: 'point_type', title: __('Point_type'),searchList:{1:__('Add_point'), 2:__('Dec_point')},formatter: Table.api.formatter.normal},
                        {field: 'rule_type', title: __('Rule_type'),searchList: Config.rule_types,formatter: Table.api.formatter.normal},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime,sortable:true},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $("#c-user_id").data("params",function () {
                return {custom:{brand_id:$("#c-brand_id").val(),status:'normal'}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-user_id").data("params",function () {
                    return {custom:{brand_id:$("#c-brand_id").val(),status:'normal'}};
                });
            });
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
