define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/user/index/index' + location.search,
                    edit_url: 'xilufitness/user/index/edit',
                    del_url: 'xilufitness/user/index/del',
                    multi_url: 'xilufitness/user/index/multi',
                    import_url: 'xilufitness/user/index/import',
                    table: 'xilufitness_user',
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
                clickToSelect: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: 'BETWEEN',sortable:true},
                        {field: 'nickname', title: __('Nickname'), operate: 'LIKE', class: 'user-name'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'avatar', title: __('Avatar'), operate: 'LIKE', events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'gender', title: __('Gender'),searchList: {0:__("Unknow"),1:__("Male"),2:__("FeMale")}, formatter: Table.api.formatter.normal},
                        {field: 'point', title: __('Point')},
                        {field: 'account', title: __('Account'), operate:'BETWEEN'},
                        {field: 'train_day', title: __('Train_day'), operate:'BETWEEN'},
                        {field: 'train_duration', title: __('Train_duration'), operate:'BETWEEN'},
                        {field: 'train_count', title: __('Train_count'), operate:'BETWEEN'},
                        {field: 'is_vip', title: __('Is_vip'), searchList: {0:__('Vip_0'),1:__('Vip_1')}, formatter: Table.api.formatter.normal},
                        {field: 'brand.brand_name', title: __('Brand_id')},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'account',
                                    text: __('余额明细'),
                                    title: __('余额明细'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                       return  Config.moduleurl + "/xilufitness/user/account?user_id="+row.id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'point',
                                    text: __('积分明细'),
                                    title: __('积分明细'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return  Config.moduleurl + "/xilufitness/user/user_point?user_id="+row.id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'collect',
                                    text: __('收藏记录'),
                                    title: __('收藏记录'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return  Config.moduleurl + "/xilufitness/user/collect?user_id="+row.id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'coupon',
                                    text: __('代金券记录'),
                                    title: __('代金券记录'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return  Config.moduleurl + "/xilufitness/user/coupon?user_id="+row.id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'media',
                                    text: __('勋章解锁记录'),
                                    title: __('勋章解锁记录'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return  Config.moduleurl + "/xilufitness/user/media?user_id="+row.id;
                                    },
                                    dropdown:'查看更多',
                                },
                                {
                                    name: 'comment',
                                    text: __('评论记录'),
                                    title: __('评论记录'),
                                    classname: 'btn btn-xs btn-magic btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row){
                                        return  Config.moduleurl + "/xilufitness/user/comment?user_id="+row.id;
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

            $(document).on("click", "td.user-name", function () {

                const user_account = $(this).text();
                //获取第一个单元格的值
                const user_id = $(this).prev().text();
                console.log(user_id);
                //跳转页面
                Backend.api.addtabs(Config.moduleurl + "/xilufitness/user/account?user_id="+user_id, user_account+"-详细信息");
            });
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
