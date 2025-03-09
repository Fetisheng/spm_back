define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/course/camp/index' + location.search,
                    add_url: 'xilufitness/course/camp/add',
                    edit_url: 'xilufitness/course/camp/edit',
                    del_url: 'xilufitness/course/camp/del',
                    multi_url: 'xilufitness/course/camp/multi',
                    import_url: 'xilufitness/course/camp/import',
                    table: 'xilufitness_camp',
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
                        {field: 'id', title: __('Id'), operate: 'BETWEEN', sortable:true},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'thumb_image', title: __('Thumb_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'lable_ids', title: __('Lable_ids'), operate: false, formatter: function (value,row) {
                                return row.lable_names || '';
                            }},
                        {field: 'camp_price', title: __('Camp_price'),operate:'BETWEEN',sortable:true},
                        {field: 'write_off_price', title: __('Write_off_price'),operate:'BETWEEN',sortable:true},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'work_camp',
                                    text: __('排课记录'),
                                    title: __('排课记录'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row) {
                                        return Config.moduleurl + '/xilufitness/work/camp?camp_id='+row.id;
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
            $("#c-lable_ids").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), lable_type:'camp'}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-lable_ids").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), lable_type:'camp'}};
                });
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $("#c-lable_ids").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), lable_type:'camp'}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-lable_ids").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), lable_type:'camp'}};
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
