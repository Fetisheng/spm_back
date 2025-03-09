define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/user/comment/index' + location.search,
                    edit_url: 'xilufitness/user/comment/edit',
                    del_url: 'xilufitness/user/comment/del',
                    table: 'xilufitness_user_comment',
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
                        {field: 'id', title: __('Id'), operate: 'BETWEEN', sortable:true},
                        {field: 'user.nickname', title: __('User_id'), operate: 'LIKE'},
                        {field: 'shop.shop_name', title: __('Shop_id'), operate: 'LIKE'},
                        {field: 'coach.coach_name', title: __('Coach_id'), operate: 'LIKE'},
                        {field: 'course_title', title: __('Course_camp_id'), operate: false, searchable:false},
                        {field: 'profession_star', title: __('Profession_star'), operate: 'BETWEEN', sortable:true,formatter: function (value,row) {
                                return row.profession_star + '分';
                            }},
                        {field: 'affinity_star', title: __('Affinity_star'), operate: 'BETWEEN', sortable:true,formatter: function (value,row) {
                                return row.affinity_star + '分';
                            }},
                        {field: 'impression_star', title: __('Impression_star'), operate: 'BETWEEN', sortable:true,formatter: function (value,row) {
                                return row.impression_star + '分';
                            }},
                        {field: 'content', title: __('Content'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate:'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'), 'hidden':__('Hidden')}, formatter: Table.api.formatter.status},
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
