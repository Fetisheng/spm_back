define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/course/index/index' + location.search,
                    add_url: 'xilufitness/course/index/add',
                    edit_url: 'xilufitness/course/index/edit',
                    del_url: 'xilufitness/course/index/del',
                    multi_url: 'xilufitness/course/index/multi',
                    import_url: 'xilufitness/course/index/import',
                    table: 'xilufitness_course',
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
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:'BETWEEN',sortable:true},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'thumb_image', title: __('Thumb_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'lable_ids', title: __('Lable_ids'), operate: false, searchable:false,formatter: function (value,row,index) {
                                return row.lable_names || '';
                            }},
                        {field: 'course_price', title: __('Course_price'),operate:'BETWEEN',sortable:true},
                        {field: 'write_off_price', title: __('Write_off_price'),operate:'BETWEEN',sortable:true},
                        {field: 'course_type', title: __('Course_type'), searchList: {1:__('Course_type_1'),2:__('Course_type_2')}, formatter: Table.api.formatter.normal},
                        {field: 'cate.cate_name', title: __('Course_cate_pid'), operate: 'LIKE'},
                        {field: 'is_index', title: __('Is_index'), searchList: {0:__('Is_index_0'),1:__('Is_index_1')}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'work_course',
                                    text: __('排课记录'),
                                    title: __('排课记录'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row) {
                                        return Config.moduleurl + '/xilufitness/work/course?course_id='+row.id;
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
            $("#c-course_cate_pid").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), pid:0}};
            });
            $("#c-course_cate_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), pid:$("#c-course_cate_pid").val()}};
            });
            $("#c-lable_ids").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), lable_type:'course'}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-course_cate_pid").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), pid:0}};
                });
                $("#c-lable_ids").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), lable_type:'course'}};
                });
            });
            $("#c-course_cate_pid").on("change",function () {
                $("#c-course_cate_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), pid:$("#c-course_cate_pid").val()}};
                });
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $("#c-course_cate_pid").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), pid:0}};
            });
            $("#c-course_cate_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), pid:$("#c-course_cate_pid").val()}};
            });
            $("#c-lable_ids").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), lable_type:'course'}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-course_cate_pid").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), pid:0}};
                });
                $("#c-lable_ids").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), lable_type:'course'}};
                });
            });
            $("#c-course_cate_pid").on("change",function () {
                $("#c-course_cate_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), pid:$("#c-course_cate_pid").val()}};
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
