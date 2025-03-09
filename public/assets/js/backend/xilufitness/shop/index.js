define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/shop/index/index' + location.search,
                    add_url: 'xilufitness/shop/index/add',
                    edit_url: 'xilufitness/shop/index/edit',
                    del_url: 'xilufitness/shop/index/del',
                    multi_url: 'xilufitness/shop/index/multi',
                    import_url: 'xilufitness/shop/index/import',
                    table: 'xilufitness_shop',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 2,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: 'BETWEEN',sortable:true},
                        {field: 'shop_name', title: __('Shop_name'), operate: 'LIKE'},
                        {field: 'shop_mobile', title: __('Shop_mobile'), operate: 'LIKE'},
                        {field: 'shop_image', title: __('Shop_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'star', title: __('Star'), operate: 'BETWEEN',sortable:true},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'work_course',
                                    text: __('课程排课记录'),
                                    title: __('课程排课记录'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return  Config.moduleurl + "/xilufitness/work/course?shop_id="+row.id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'work_camp',
                                    text: __('活动排课记录'),
                                    title: __('活动排课记录'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return  Config.moduleurl + "/xilufitness/work/camp?shop_id="+row.id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'order_course',
                                    text: __('团课订单记录'),
                                    title: __('团课订单记录'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return  Config.moduleurl + "/xilufitness/order/course?shop_id="+row.id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'order_personal',
                                    text: __('私教课订单记录'),
                                    title: __('私教课订单记录'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return  Config.moduleurl + "/xilufitness/order/personal?shop_id="+row.id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'order_camp',
                                    text: __('活动订单记录'),
                                    title: __('活动订单记录'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return  Config.moduleurl + "/xilufitness/order/camp?shop_id="+row.id;
                                    },
                                    dropdown:'查看更多',
                                },
                            ],
                            formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'xilufitness/shop/index/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '140px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'xilufitness/shop/index/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'xilufitness/shop/index/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
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
        lat_lng: function () {
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
