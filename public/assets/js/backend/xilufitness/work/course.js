define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xilufitness/work/course/index' + location.search,
                    add_url: 'xilufitness/work/course/add',
                    edit_url: 'xilufitness/work/course/edit',
                    del_url: 'xilufitness/work/course/del',
                    multi_url: 'xilufitness/work/course/multi',
                    import_url: 'xilufitness/work/course/import',
                    table: 'xilufitness_work_course',
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
                queryParams:function(params){
                    params.filter = JSON.parse(params.filter);
                    if(Config.course_id > 0){
                        params.filter.course_id = Config.course_id;
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
                        {field: 'id', title: __('Id'),operate: 'BETWEEN', sortable:true},
                        {field: 'courses.title', title: __('Course_id'),operate: 'LIKE'},
                        {field: 'course_type', title: __('Course_type'), searchList: {1:__('Course_type_1'),2:__('Course_type_2')}, formatter: Table.api.formatter.normal},
                        {field: 'class_time', title: __('Class_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'start_at', title: __('Start_at'), operate: false, searchable: false},
                        {field: 'end_at', title: __('End_at'), operate: false, searchable: false},
                        {field: 'course_price', title: __('Course_price'), operate:'BETWEEN'},
                        {field: 'write_off_price', title: __('Write_off_price'), operate:'BETWEEN'},
                        {field: 'class_count', title: __('Class_count'), sortable:true, searchable:false},
                        {field: 'sign_count', title: __('Sign_count'), sortable:true, searchable:false},
                        {field: 'wait_count', title: __('Wait_count'), sortable:true, searchable:false},
                        {field: 'shop.shop_name', title: __('Shop_id'),operate: 'LIKE'},
                        {field: 'coach.coach_name', title: __('Coach_id'),operate: 'LIKE'},
                        {field: 'brand.brand_name', title: __('Brand_id'),operate: 'LIKE'},
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
                                        if(row.course_type == 1){
                                            return Config.moduleurl + '/xilufitness/order/course?data_id='+row.id+'&order_type=1'+'&brand_id='+row.brand_id;
                                        } else {
                                            return Config.moduleurl + '/xilufitness/order/personal?data_id='+row.id+'&order_type=2'+'&brand_id='+row.brand_id;
                                        }
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
            $("#c-course_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
            });
            $("#c-shop_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-course_id").data("params",function () {
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
                        url:Config.moduleurl + '/xilufitness/work/course/selectCoach',
                        data:{
                            shop_ids:shop_ids,
                            course_id:$("#c-course_id").val()
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

            //监控单选框数值变化
            $('input[name="date_type"]').on("change",function () {
                const selectedValue = $(this).val();
                if (selectedValue === 'week') {
                    console.log(selectedValue);
                    $("#week-select").show();
                    $("#week-select").find(":input").attr("disabled", false);
                    $("#day-select").hide();
                    $("#day-select").find(":input").attr("disabled", true);
                }
                if (selectedValue === 'month') {
                    console.log(selectedValue);
                    $("#week-select").hide();
                    $("#week-select").find(":input").attr("disabled", true);
                    $("#day-select").show();
                    $("#day-select").find(":input").attr("disabled", false);
                }
            });

            Controller.api.bindevent();
        },
        edit: function () {
            $("#c-course_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
            });
            $("#c-shop_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
            });
            $("#c-coach_id").data("params",function () {
                return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), shop_ids:['like','%'+$("#c-shop_id").val()+'%']}};
            });
            $("#c-brand_id").on("change",function () {
                $("#c-course_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
                });
                $("#c-shop_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val()}};
                });
                $("#c-coach_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), shop_ids:['like','%'+$("#c-shop_id").val()+'%']}};
                });
            });
            $("#c-shop_id").on("change",function () {
                $("#c-coach_id").data("params",function () {
                    return {custom:{status:'normal', brand_id:$("#c-brand_id").val(), shop_ids:['like','%'+$("#c-shop_id").val()+'%']}};
                });
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
