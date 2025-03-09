define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/coach/index/index' + location.search,
                    add_url: 'xilufitness/coach/index/add',
                    edit_url: 'xilufitness/coach/index/edit',
                    del_url: 'xilufitness/coach/index/del',
                    multi_url: 'xilufitness/coach/index/multi',
                    import_url: 'xilufitness/coach/index/import',
                    account_url:'xilufitness/coach/account',
                    cash_url:'xilufitness/coach/cash',
                    withdraw_url:'xilufitness/coach/withdraw',
                    report_url:'xilufitness/coach/report',
                    course_url:'xilufitness/work/course',
                    camp_url:'xilufitness/work/camp',
                    table: 'xilufitness_coach',
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
                        {field: 'coach_name', title: __('Coach_name'), operate: 'LIKE'},
                        {field: 'coach_mobile', title: __('Coach_mobile'), operate: 'LIKE'},
                        {field: 'coach_avatar', title: __('Coach_avatar'), operate: 'LIKE', events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'group.group_name', title: __('Coach_group_id'), operate: 'LIKE'},
                        {field: 'coach_sex', title: __('Coach_sex'), searchList: {"male":__('Male'),"female":__('Female'),"unknow":__('Unknow')}, formatter: Table.api.formatter.normal},
                        {field: 'lable_ids', title: __('Lable_ids'), operate: false, searchable:false,formatter:function (value,row) {
                                return row.lable_names;
                            }},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'shop_ids', title: __('Shop_ids'), operate: false, searchable:false,formatter: function (value,row) {
                                return row.shop_names;
                            }},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'course',
                                    text: __('课程排课'),
                                    title: __('课程排课'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return $.fn.bootstrapTable.defaults.extend.course_url+"?coach_id="+row.id+'&brand_id='+row.brand_id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'camp',
                                    text: __('活动排课'),
                                    title: __('活动排课'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return $.fn.bootstrapTable.defaults.extend.camp_url+"?coach_id="+row.id+'&brand_id='+row.brand_id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'account',
                                    text: __('基本信息'),
                                    title: __('基本信息'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return $.fn.bootstrapTable.defaults.extend.account_url+"?coach_id="+row.id+'&brand_id='+row.brand_id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'cash',
                                    text: __('收入流水'),
                                    title: __('收入流水'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return $.fn.bootstrapTable.defaults.extend.cash_url+"?coach_id="+row.id+'&brand_id='+row.brand_id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'withdraw',
                                    text: __('提现记录'),
                                    title: __('提现记录'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return $.fn.bootstrapTable.defaults.extend.withdraw_url+"?coach_id="+row.id+'&brand_id='+row.brand_id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'report',
                                    text: __('请假报备'),
                                    title: __('请假报备'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return $.fn.bootstrapTable.defaults.extend.report_url+"?coach_id="+row.id+'&brand_id='+row.brand_id;
                                    },
                                    dropdown:'查看更多',
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
            $("#c-shop_ids").data("params",function () {
                return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal' }};
            })
            $("#c-coach_group_id").data("params",function () {
                return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal' }};
            })
            $("#c-lable_ids").data("params",function () {
                return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal', lable_type:'coach' }};
            })
            $("#c-brand_id").on("change",function () {
                $("#c-shop_ids").data("params",function () {
                    return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal' }};
                })
                $("#c-coach_group_id").data("params",function () {
                    return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal' }};
                })
                $("#c-lable_ids").data("params",function () {
                    return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal', lable_type:'coach' }};
                })
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $("#c-shop_ids").data("params",function () {
                return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal' }};
            })
            $("#c-coach_group_id").data("params",function () {
                return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal' }};
            })
            $("#c-lable_ids").data("params",function () {
                return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal', lable_type:'coach' }};
            })
            $("#c-brand_id").on("change",function () {
                $("#c-shop_ids").data("params",function () {
                    return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal' }};
                })
                $("#c-coach_group_id").data("params",function () {
                    return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal' }};
                })
                $("#c-lable_ids").data("params",function () {
                    return {custom:{brand_id:$("#c-brand_id").val() || 0, status:'normal', lable_type:'coach' }};
                })
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
