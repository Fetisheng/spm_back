define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/card/card_package/index',
                    add_url: 'xilufitness/card/card_package/add',
                    edit_url: 'xilufitness/card/card_package/edit',
                    del_url: 'xilufitness/card/card_package/del',
                    multi_url: 'xilufitness/card/card_package/multi',
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
                        {field: 'package_name', title: __('套餐名称') , operate: 'LIKE'},
                        {field: 'package_desc', title: __('套餐简介') , operate: false},
                        {field: 'package_avatar', title: __('展示封面'),operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'total_price', title: __('总售价') },
                        {field: 'brand.brand_name', title: __('所属小程序'), operate: 'LIKE'},
                        {field: 'sort', title: __('展示顺序'), operate: false},
                        {field: 'status', title: __('是否启用'), searchList: {"1":__('启用'),"0":__('禁用')},formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        getCategoryStr: function (category) {
            let html_str = "";
            if (category) {
                html_str = "<tr category-id="+category.id+">" +
                    "<td>"+category.cardname+"</td>" +
                    "<td>"+category.cardtypename+"</td>";
                if (category.cardtype === '1' || category.cardtype === '4') {
                    html_str += "<td>"+category.timescardquota+"次</td>";
                }else {
                    html_str += "<td>-</td>";
                }
                html_str +=
                    "<td>"+category.expiretimes+"</td>" +
                    "<td>"+category.cardprice+"</td>" +
                    "<td><i class=\"fa fa-trash delete-category\" style='cursor: pointer' category-id="+category.id+"></i></td>" +
                    "</tr>";
            }
            console.log(html_str)
            return html_str;
        },
        add: function () {
            const self = this;
            $("#add_card").on("click",function () {
                const category_id = $("#card_category_id").val();
                console.log(category_id);
                if(category_id !== null && category_id !== ''&& category_id !== '请选择'){
                    let category_ids = $("#c-category_ids").val();
                    if (category_ids !== '') {
                        category_ids = category_ids + ',' + category_id;
                    }else {
                        category_ids = category_id;
                    }
                    $("#c-category_ids").val(category_ids);

                    Fast.api.ajax({
                        url:Config.moduleurl + '/xilufitness/card/user_card/get_category_info',
                        data:{
                            category_id: category_id
                        },
                        method:'POST'
                    },function (res) {
                        if(res){
                            $("#category-box").append(self.getCategoryStr(res))
                            Controller.api.bindevent();
                        }
                        return false;
                    },function (error) {
                        console.log(error);
                        Toastr.error(__('Select_coach_fail'));
                        return false;
                    });
                }else {
                    Toastr.error(__('请选择卡种'));
                }
            });


            $(document).on('click', '.fa.fa-trash.delete-category', function() {
                const category_id = $(this).attr('category-id');
                $(this).parent().parent().remove();
                let category_ids = $("#c-category_ids").val();
                if (category_ids.includes(',' + category_id)) {
                    category_ids = category_ids.replace(','+category_id,'');
                }else {
                    category_ids = category_ids.replace(category_id,'');
                }
                $("#c-category_ids").val(category_ids);
            });

            Controller.api.bindevent();
        },
        edit: function () {

            const self = this;
            $("#add_card").on("click",function () {
                const category_id = $("#card_category_id").val();
                console.log(category_id);
                if(category_id !== null && category_id !== ''&& category_id !== '请选择'){
                    let category_ids = $("#c-category_ids").val();
                    if (category_ids !== '') {
                        category_ids = category_ids + ',' + category_id;
                    }else {
                        category_ids = category_id;
                    }
                    $("#c-category_ids").val(category_ids);

                    Fast.api.ajax({
                        url:Config.moduleurl + '/xilufitness/card/user_card/get_category_info',
                        data:{
                            category_id: category_id
                        },
                        method:'POST'
                    },function (res) {
                        if(res){
                            $("#category-box").append(self.getCategoryStr(res))
                            Controller.api.bindevent();
                        }
                        return false;
                    },function (error) {
                        console.log(error);
                        Toastr.error(__('Select_coach_fail'));
                        return false;
                    });
                }else {
                    Toastr.error(__('请选择卡种'));
                }
            });


            $(document).on('click', '.fa.fa-trash.delete-category', function() {
                const category_id = $(this).attr('category-id');
                $(this).parent().parent().remove();
                let category_ids = $("#c-category_ids").val();
                if (category_ids.includes(',' + category_id)) {
                    category_ids = category_ids.replace(','+category_id,'');
                }else {
                    category_ids = category_ids.replace(category_id,'');
                }
                $("#c-category_ids").val(category_ids);
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