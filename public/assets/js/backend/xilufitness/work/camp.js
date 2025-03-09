define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/work/camp/index' + location.search,
                    add_url: 'xilufitness/work/camp/add',
                    edit_url: 'xilufitness/work/camp/edit',
                    del_url: 'xilufitness/work/camp/del',
                    multi_url: 'xilufitness/work/camp/multi',
                    import_url: 'xilufitness/work/camp/import',
                    table: 'xilufitness_work_camp',
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
                queryParams:function(params){
                    params.filter = JSON.parse(params.filter);
                    if(Config.camp_id > 0){
                        params.filter.camp_id = Config.camp_id;
                    }
                    if(Config.shop_id > 0){
                        params.filter.shop_id = Config.shop_id;
                    }
                    if(Config.coach_id > 0){
                        params.filter.coach_id = Config.coach_id;
                    }
                    if(Config.brand_id > 0){
                        params.filter.brand_id = Config.brand_id;
                    }
                    params.filter = JSON.stringify(params.filter);
                    return params;
                },
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: 'BETWEEN', sortable:true},
                        {field: 'camps.title', title: __('Camp_id'), operate: 'LIKE'},
                        {field: 'start_at', title: __('Start_at'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'end_at', title: __('End_at'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'class_duration', title: __('Class_duration'), operate: 'BETWEEN'},
                        {field: 'class_count', title: __('Class_count'), operate: 'BETWEEN'},
                        {field: 'camp_count', title: __('Camp_count'), operate: 'BETWEEN'},
                        {field: 'total_count', title: __('Total_count'), operate: 'BETWEEN'},
                        {field: 'shop.shop_name', title: __('Shop_id'), operate: 'LIKE'},
                        {field: 'coach.coach_name', title: __('Coach_id'), operate: 'LIKE'},
                        {field: 'brand.brand_name', title: __('Brand_id'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"complete":__('Complete'),"failed":__('Failed'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'order',
                                    text: __('报名列表'),
                                    title: __('报名列表'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: function (row) {
                                        return Config.moduleurl + '/xilufitness/order/camp?data_id='+row.id+'&order_type=3'+'&brand_id='+row.brand_id;
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
            $("#c-camp_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
            });
            $("#c-shop_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-camp_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
                });
                $("#c-shop_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
                });
            });
            $("#c-shop_id").on("change",function () {
                let shop_ids = $("#c-shop_id").val();
                if(shop_ids){
                    Fast.api.ajax({
                        url:Config.moduleurl + '/xilufitness/work/camp/selectCoach',
                        data:{
                            shop_ids:shop_ids,
                            camp_id:$("#c-camp_id").val()
                        },
                        method:'POST'
                    },function (res) {
                        console.log('res',res);
                        if(res.html){
                            $("#coach_list").html(res.html).parent().show();
                            Controller.api.bindevent();
                        }
                        return false;
                    },function (error) {
                        Toastr.error(__('Select_coach_fail'));
                        return false;
                    });
                }
            });
            //新增计划
            $('.addPlan').on('click',function () {
                var html = '<tr>';
                html += '<td><input placeholder="请选择" data-rule="required" class="form-control datetimepicker" data-date-format="YYYY-MM-DD" name="row[day_date][]" type="text"></td>';
                html += '<td><input placeholder="请选择" data-rule="required" class="form-control datetimepicker" data-date-format="HH:mm" name="row[day_start_at][]" type="text"></td>';
                html += '<td><input placeholder="请选择" data-rule="required" class="form-control datetimepicker" data-date-format="HH:mm" name="row[day_end_at][]" type="text"></td>';
                html += '<td><span class="btn btn-danger delPlan"><i class="fa fa-trash"></i>'+__('Del_plan')+'</span></td>';
                html += '</tr>';
                $("#plan_list").append(html);
                Controller.api.bindevent();
            });
            //删除计划
            $("#plan_list").on("click",".delPlan",function () {
                $(this).parent().parent().remove();
                Controller.api.bindevent();
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $("#c-camp_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
            });
            $("#c-shop_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
            });
            $("#c-coach_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), shop_ids:['like','%'+$("#c-shop_id").val()+'%']}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-camp_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
                });
                $("#c-shop_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
                });
                $("#c-coach_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), shop_ids:['like','%'+$("#c-shop_id").val()+'%']}};
                });
            });
            //新增计划
            $('.addPlan').on('click',function () {
                var html = '<tr>';
                html += '<td><input placeholder="请选择" data-rule="required" class="form-control datetimepicker" data-date-format="YYYY-MM-DD" name="row[day_date][]" type="text"></td>';
                html += '<td><input placeholder="请选择" data-rule="required" class="form-control datetimepicker" data-date-format="HH:mm" name="row[day_start_at][]" type="text"></td>';
                html += '<td><input placeholder="请选择" data-rule="required" class="form-control datetimepicker" data-date-format="HH:mm" name="row[day_end_at][]" type="text"></td>';
                html += '<td><span class="btn btn-danger delPlan"><i class="fa fa-trash"></i>'+__('Del_plan')+'</span></td>';
                html += '</tr>';
                $("#plan_list").append(html);
                Controller.api.bindevent();
            });
            //删除计划
            $("#plan_list").on("click",".delPlan",function () {
                $(this).parent().parent().remove();
                Controller.api.bindevent();
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
