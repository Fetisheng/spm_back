define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/activity/coupon/index' + location.search,
                    add_url: 'xilufitness/activity/coupon/add',
                    edit_url: 'xilufitness/activity/coupon/edit',
                    del_url: 'xilufitness/activity/coupon/del',
                    multi_url: 'xilufitness/activity/coupon/multi',
                    import_url: 'xilufitness/activity/coupon/import',
                    table: 'xilufitness_activity_coupon',
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
                        {field: 'meet_amount', title: __('Meet_amount'), operate:'BETWEEN'},
                        {field: 'discount_amount', title: __('Discount_amount'), operate:'BETWEEN'},
                        {field: 'expire_day', title: __('Expire_day'), operate:'BETWEEN'},
                        {field: 'coupon_count', title: __('Coupon_count'), operate:'BETWEEN'},
                        {field: 'is_activity', title: __('Is_activity'), searchList: {0:__('Is_activity_0'),1:__('Is_activity_1')}, formatter: Table.api.formatter.normal},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'user_coupon',
                                    text: __('领取记录'),
                                    title: __('领取记录'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row) {
                                        return Config.moduleurl + '/xilufitness/user/coupon?coupon_id='+row.id;
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
            $("[name='row[is_activity]']").on("change",function () {
                if($(this).val() == 1){
                    $("#c-invite_num").parent().parent().show();
                } else {
                    $("#c-invite_num").parent().parent().hide();
                }
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $("[name='row[is_activity]']").on("change",function () {
                if($(this).val() == 1){
                    $("#c-invite_num").parent().parent().show();
                } else {
                    $("#c-invite_num").parent().parent().hide();
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
