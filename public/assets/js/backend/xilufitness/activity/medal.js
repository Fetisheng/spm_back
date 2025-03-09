define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/activity/medal/index' + location.search,
                    add_url: 'xilufitness/activity/medal/add',
                    edit_url: 'xilufitness/activity/medal/edit',
                    del_url: 'xilufitness/activity/medal/del',
                    multi_url: 'xilufitness/activity/medal/multi',
                    import_url: 'xilufitness/activity/medal/import',
                    table: 'xilufitness_activity_medal',
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
                        {field: 'medal_name', title: __('Medal_name'), operate: 'LIKE'},
                        {field: 'thumb_image', title: __('Thumb_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'thumb_active_image', title: __('Thumb_active_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'user_media',
                                    text: __('解锁记录'),
                                    title: __('解锁记录'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row) {
                                        return Config.moduleurl + '/xilufitness/user/media?media_id='+row.id;
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
