define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/card/user_card/index',
                    add_url: 'xilufitness/card/user_card/add',
                    edit_url: 'xilufitness/card/user_card/edit',
                    del_url: 'xilufitness/card/user_card/del',
                    multi_url: 'xilufitness/card/user_card/multi',
                    check_url: 'xilufitness/card/user_card/check',
                    table: 'category',
                }
            });

            var table = $("#card-table");

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
                        {field: 'id', title: __('编号'), sortable: true, operate: false},
                        {field: 'user.nickname', title: __('持卡人') , operate: 'LIKE'},
                        {field: 'user.avatar', title: __('头像') , operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'user.mobile', title: __('手机号')},
                        {field: 'card_no', title: __('会员卡卡号')},
                        {field: 'category.cardname', title: __('会员卡名称') , operate: 'LIKE'},
                        {field: 'category.cardtype', title: __('会员卡类型') , searchList: {"1":__('次卡'),"2":__('时长卡'),"4":__('课程次卡')},formatter: Table.api.formatter.status},
                        {field: 'left_times_count', title: __('次卡剩余次数') },
                        {field: 'already_share_times', title: __('分享次数') },
                        {field: 'left_share_times', title: __('剩余分享次数') },
                        {field: 'open_card_time', title: __('购买日期'),formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'effective_date', title: __('生效时间'),formatter: Table.api.formatter.date, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'expire_time', title: __('到期日期'),formatter: Table.api.formatter.date, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'brand.brand_name', title: __('所属小程序'), operate: 'LIKE'},
                        {field: 'shop.shop_name', title: __('售卡门店'), operate: 'LIKE'},
                        {field: 'status', title: __('卡状态'), searchList: {"1":__('正常'),"2":__('过期'),"3":__('退卡'),"4":__('停卡'),"0":__('未开卡')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: '一键核销',
                                    text: __('一键核销'),
                                    title: __('一键核销'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-chevron-up',
                                    url: function (row){
                                        return  Config.moduleurl + '/xilufitness/card/user_card/check?ids='+row.id;
                                    }
                                }
                            ],
                            formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        getHtmlStr: function (card){
            let html_str = "";
            if (card) {
                html_str = "<div class=\"box\"><div class=\"box-body no-padding\"><table class=\"table table-condensed\">" +
                    "<tbody><tr><td>会员卡名称</td><td>会员卡类型</td>";
                if (card.cardtype === '1' || card.cardtype === '4') {
                    html_str = html_str + "<td>次卡额度</td>";
                }
                html_str = html_str + "<td>会员卡有效期</td><td>售价(元)</td>";
                html_str = html_str + "</tr>" +
                    "<tr>" +
                    "<td>"+card.cardname+"</td>" +
                    "<td>"+card.cardtypename+"</td>";
                if (card.cardtype === '1' || card.cardtype === '4') {
                    html_str = html_str + "<td>"+card.timescardquota+"次</td>";
                }
                html_str = html_str + "<td>"+card.expiretimes+"</td><td>"+card.cardprice+"</td></tr></tbody></table></div></div>";
            }
            console.log(html_str)
            return html_str;
        },

        add: function () {
            const self = this;
            $("#card_category_id").on("change",function () {
                $("#card_info").html("").parent().hide();
                const category_id = $("#card_category_id").val();
                console.log(category_id);
                if(category_id){
                    Fast.api.ajax({
                        url:Config.moduleurl + '/xilufitness/card/user_card/get_category_info',
                        data:{
                            category_id:category_id
                        },
                        method:'POST'
                    },function (res) {
                        console.log('res',res);
                        if(res){
                            $("#card_info").html(self.getHtmlStr(res)).parent().show();
                            Controller.api.bindevent();
                        }
                        return false;
                    },function (error) {
                        console.log(error);
                        Toastr.error(__('Select_coach_fail'));
                        return false;
                    });
                }
            });

            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        check: function () {
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