define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/course/cate/index' + location.search,
                    add_url: 'xilufitness/course/cate/add',
                    edit_url: 'xilufitness/course/cate/edit',
                    del_url: 'xilufitness/course/cate/del',
                    multi_url: 'xilufitness/course/cate/multi',
                    import_url: 'xilufitness/course/cate/import',
                    table: 'xilufitness_course_cate',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                commonSearch: false,
                search: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: 'BETWEEN', sortable:true},
                        {field: 'cate_name', title: __('Cate_name'), operate: false,formatter:function (value, row, index) {
                                return value.toString().replace(/(&|&amp;)nbsp;/g, '&nbsp;');
                            }},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'brand_name', title: __('Brand_id'), operate: false},
                        {field: 'weigh', title: __('Weigh'), operate: false, sortable:true},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $("#c-pid").data("params",function () {
                return {custom:{brand_id:$("#c-brand_id").val(), status:'normal', pid:0}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-pid").data("params",function () {
                    return {custom:{brand_id:$("#c-brand_id").val(), status:'normal', pid:0}};
                });
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $("#c-pid").data("params",function () {
                return {custom:{brand_id:$("#c-brand_id").val(), status:'normal', pid:0}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-pid").data("params",function () {
                    return {custom:{brand_id:$("#c-brand_id").val(), status:'normal', pid:0}};
                });
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
