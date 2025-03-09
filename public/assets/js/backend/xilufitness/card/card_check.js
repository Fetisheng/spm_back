define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/card/card_check/index',
                    add_url: 'xilufitness/card/card_check/add',
                    edit_url: 'xilufitness/card/card_check/edit',
                    del_url: 'xilufitness/card/card_check/del',
                    multi_url: 'xilufitness/card/card_check/multi',
                    table: 'category',
                }
            });

            var table = $("#card-table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('编号'), sortable: true, operate: false},
                        {field: 'user_card_id', title: __('会员卡ID') },
                        {field: 'card.card_no', title: __('会员卡号') },
                        {field: 'card_type_name', title: __('会员卡类型') },
                        {field: 'user_id', title: __('使用人员ID') , operate: false},
                        {field: 'user.nickname', title: __('使用人员') , operate: 'LIKE'},
                        {field: 'admin_user_id', title: __('核销人员ID') , operate: false},
                        {field: 'admin.nickname', title: __('核销人员') , operate: 'LIKE'},
                        {field: 'shop.shop_name', title: __('门店名称') , operate: false},
                        {field: 'shop.address', title: __('门店地址') , operate: false},
                        {field: 'check_time', title: __('核销时间') },
                        {field: 'check_type', title: __('核销方式'),searchList: {"1":__('二维码核销'),"2":__('后台核销')},formatter: Table.api.formatter.status},
                        {field: 'brand.brand_name', title: __('所属小程序'), operate: 'LIKE'},
                        {field: 'status', title: __('核销状态'), searchList: {"1":__('核销成功'),"0":__('核销失败')},formatter: Table.api.formatter.status}
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