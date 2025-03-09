define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/banner/index/index' + location.search,
                    add_url: 'xilufitness/banner/index/add',
                    edit_url: 'xilufitness/banner/index/edit',
                    del_url: 'xilufitness/banner/index/del',
                    multi_url: 'xilufitness/banner/index/multi',
                    import_url: 'xilufitness/banner/index/import',
                    table: 'xilufitness_banner',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: 'BETWEEN', sortable:true},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'thumb_image', title: __('Thumb_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'banner_position', title: __('Banner_position'),searchList: Config.positionList, formatter: Table.api.formatter.normal},
                        {field: 'is_redirect', title: __('Is_redirect'),searchList: {0:__('Is_redirect_0'),1:__('Is_redirect_1'),2:__('Is_redirect_2'),3:__('Is_redirect_3')}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'weigh', title: __('Weigh'), operate: false, sortable:true, searchable:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $("#c-brand_id").on("change",function () {
                $("#c-data_id").data("params",function () {
                    return {custom:{status:"normal", brand_id:$("#c-brand_id").val()}};
                });
            });
            $("[name='row[is_redirect]']").on("change",function () {
                let is_redirect = $(this).val();
                let brand_id = $("#c-brand_id").val();
                if(is_redirect == 1){
                    let html = ' <input id="c-data_id" placeholder="请输入" data-rule="required" data-source="xilufitness/course/index" data-field="title" data-params=\'{"custom[status]":"normal","custom[brand_id]":"'+brand_id+'"}\' class="form-control selectpage" name="row[data_id]" type="text" value="">';
                    $("#data_html").html(html);
                    $("#data_html").parent().show();
                    Controller.api.bindevent();
                } else if(is_redirect == 2){
                    let html = ' <input id="c-data_id" placeholder="请输入" data-rule="required" data-source="xilufitness/course/camp" data-field="title" data-params=\'{"custom[status]":"normal","custom[brand_id]":"'+brand_id+'"}\' class="form-control selectpage" name="row[data_id]" type="text" value="">';
                    $("#data_html").html(html);
                    $("#data_html").parent().show();
                    Controller.api.bindevent();
                } else if(is_redirect == 3){
                    let html = ' <input id="c-data_id" placeholder="请输入" data-rule="required" data-source="xilufitness/shop/index" data-field="shop_name" data-params=\'{"custom[status]":"normal","custom[brand_id]":"'+brand_id+'"}\' class="form-control selectpage" name="row[data_id]" type="text" value="">';
                    $("#data_html").html(html);
                    $("#data_html").parent().show();
                    Controller.api.bindevent();
                } else {
                    $("#data_html").parent().hide();
                }
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $("#c-data_id").data("params",function () {
                return {custom:{status:"normal", brand_id:$("#c-brand_id").val()}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-data_id").data("params",function () {
                    return {custom:{status:"normal", brand_id:$("#c-brand_id").val()}};
                });
            });
            $("[name='row[is_redirect]']").on("change",function () {
                let is_redirect = $(this).val();
                let brand_id = $("#c-brand_id").val();
                if(is_redirect == 1){
                    let html = ' <input id="c-data_id" placeholder="请输入" data-rule="required" data-source="xilufitness/course/index" data-field="title" data-params=\'{"custom[status]":"normal","custom[brand_id]":"'+brand_id+'"}\' class="form-control selectpage" name="row[data_id]" type="text" value="">';
                    $("#data_html").html(html);
                    $("#data_html").parent().show();
                    Controller.api.bindevent();
                } else if(is_redirect == 2){
                    let html = ' <input id="c-data_id" placeholder="请输入" data-rule="required" data-source="xilufitness/course/camp" data-field="title" data-params=\'{"custom[status]":"normal","custom[brand_id]":"'+brand_id+'"}\' class="form-control selectpage" name="row[data_id]" type="text" value="">';
                    $("#data_html").html(html);
                    $("#data_html").parent().show();
                    Controller.api.bindevent();
                } else if(is_redirect == 3){
                    let html = ' <input id="c-data_id" placeholder="请输入" data-rule="required" data-source="xilufitness/shop/index" data-field="shop_name" data-params=\'{"custom[status]":"normal","custom[brand_id]":"'+brand_id+'"}\' class="form-control selectpage" name="row[data_id]" type="text" value="">';
                    $("#data_html").html(html);
                    $("#data_html").parent().show();
                    Controller.api.bindevent();
                } else {
                    $("#data_html").parent().hide();
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
