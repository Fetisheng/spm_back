define(['jquery','bootstrap','timetables','table'], function ($, undefined, Timetables, Table) {
    var timetable;
    var course_list = [];
    var timeType = [];

    var option = {
        el: '#coursesTable',
        timetables: [],
        week: [],
        timetableType: [],
        highlightWeek: {},
        gridOnClick: function (e) {
            const course_content = course_list[e.num][e.index - 1];
            if (Array.isArray(course_content)) {
                const week_name = e.week;
                const week_left = timeType[e.index - 1][0].name;
                if (course_content.length > 0) {
                    let trs = "<tr>" +
                        "<td id=\"modal-week-time\" rowspan=\"" + course_content.length + "\" style=\"vertical-align: middle;\">" + week_left + "</td>" +
                        "<td class=\"modal-week-course\">" + course_content[0].title + "</td>" +
                        "<td>" +
                        "<a href=\"javascript:;\" class=\"btn btn-xs btn-success btn-editone course_edit_btn\" data-field-index="+course_content[0].work_course_id+" data-original-title=\"编辑\"><i class=\"fa fa-pencil\"></i></a>" +
                        "<a href=\"javascript:;\" class=\"btn btn-xs btn-danger btn-delone course_del_btn\" data-field-index="+course_content[0].work_course_id+" data-original-title=\"删除\"><i class=\"fa fa-trash\"></i></a>" +
                        "</td>" +
                        "</tr>";
                    if (course_content.length > 1) {
                        for (let i = 1; i < course_content.length; i++) {
                            const content = course_content[i].title;
                            trs += "<tr>" +
                                "<td class=\"modal-week-course\">" + content + "</td>" +
                                "<td>" +
                                "<a href=\"javascript:;\" class=\"btn btn-xs btn-success btn-editone course_edit_btn\" data-field-index="+course_content[0].work_course_id+" data-original-title=\"编辑\"><i class=\"fa fa-pencil\"></i></a>" +
                                "<a href=\"javascript:;\" class=\"btn btn-xs btn-danger btn-delone course_del_btn\" data-field-index="+course_content[0].work_course_id+" data-original-title=\"删除\"><i class=\"fa fa-trash\"></i></a>" +
                                "</td>" +
                                "</tr>";
                        }
                    }
                    $('#modal-week-tb').html(trs);
                    $('#mymodal').modal('show');
                }
            }
        },
        styles: {
            Gheight: 60,
            leftHandWidth: 85
        }
    };
    var shop_id;
    var day;
    var offset = 0;


    var Controller = {
        formatDate:function formatDate(date, format) {
            const map = {
                'Y': date.getFullYear(), // 年份
                'm': ('0' + (date.getMonth() + 1)).slice(-2), // 月份，从0开始，所以需要+1
                'd': ('0' + date.getDate()).slice(-2), // 日期
                'H': ('0' + date.getHours()).slice(-2), // 小时
                'i': ('0' + date.getMinutes()).slice(-2), // 分钟
                's': ('0' + date.getSeconds()).slice(-2) // 秒
            };
            return format.replace(/Y|m|d|H|i|s/g, matched => map[matched]);
        },
        getFirstDayOfWeek: function () {
            const now = new Date();
            const day = now.getDay();
            const diff = now.getDate() - day + (day === 0 ? -6 : 1); // 获取本周第一天的日期
            const firstDayOfWeek = new Date(now.setDate(diff));
            return this.formatDate(firstDayOfWeek, 'Y-m-d');
        },
        getLastWeek: function (day) {
            console.log(day);
            const oldDay = new Date(Date.parse(day));
            oldDay.setDate(oldDay.getDate() - 7);
            return this.formatDate(oldDay, 'Y-m-d')
        },
        getNextWeek: function (day) {
            let oldDay = new Date(Date.parse(day));
            oldDay.setDate(oldDay.getDate() + 7);
            return this.formatDate(oldDay, 'Y-m-d')
        },
        refreshCourseHeader: function (day) {
            let firstDay = new Date(Date.parse(day));
            var title = ['周一', '周二', '周三', '周四', '周五','周六','周日']
            if (window.innerWidth > 360) {
                for (let i = 0; i < 7; i++) {
                    let tempDay = new Date(firstDay.getTime());
                    tempDay.setDate(tempDay.getDate() + i);
                    const dayHead = this.formatDate(tempDay, 'Y-m-d');
                    title[i] += "(" + dayHead + ")";
                }
                option.week = title;
            }

            if (offset === 0) {
                option.highlightWeek = new Date().getDay()===0 ? 7 : new Date().getDay();
            }else {
                option.highlightWeek = {};
            }
        },
        init_course: function (shop_id,day) {
            $.ajax({
                url: Config.moduleurl + '/xilufitness/work/schedule/get_data',
                type: 'get',
                dataType: 'json',
                data: {
                    begin_date: day,
                    shop_id: shop_id
                },
                success: function (ret) {
                    if (ret.code === 1) {
                        option.timetables = ret.data;
                        timetable.setOption(option);
                        course_list = ret.data;
                    } else {
                        Backend.api.toastr.error(ret.msg);
                    }
                }, error: function (e) {
                    Backend.api.toastr.error(e.message);
                }
            });
        },
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

            const self = this;
            option.timetables = [
                ['', '', '', '', '', '', '', '', '', '', '', '','', ''],
                ['', '', '', '', '', '', '', '', '', '', '', '','', ''],
                ['', '', '', '', '', '', '', '', '', '', '', '','', ''],
                ['', '', '', '', '', '', '', '', '', '', '', '','', ''],
                ['', '', '', '', '', '', '', '', '', '', '', '','', ''],
                ['', '', '', '', '', '', '', '', '', '', '', '','', ''],
                ['', '', '', '', '', '', '', '', '', '', '', '','', ''],
            ];
            option.week = window.innerWidth > 360 ? ['周一', '周二', '周三', '周四', '周五','周六','周日'] :
                ['一', '二', '三', '四', '五','六','日'];
            option.highlightWeek = new Date().getDay()===0 ? 7 : new Date().getDay();
            option.timetableType = [
                [{index: '1', name: '8:00~9:00'}, 1],
                [{index: '2', name: '9:00~10:00'}, 1],
                [{index: '3', name: '10:00~11:00'}, 1],
                [{index: '4', name: '11:00~12:00'}, 1],
                [{index: '5', name: '12:00~13:00'}, 1],
                [{index: '6', name: '13:00~14:00'}, 1],
                [{index: '7', name: '14:00~15:00'}, 1],
                [{index: '8', name: '15:00~16:00'}, 1],
                [{index: '9', name: '16:00~17:00'}, 1],
                [{index: '10', name: '17:00~18:00'}, 1],
                [{index: '11', name: '18:00~19:00'}, 1],
                [{index: '12', name: '19:00~20:00'}, 1],
                [{index: '13', name: '20:00~21:00'}, 1],
                [{index: '14', name: '21:00~22:00'}, 1],
            ];
            timeType = option.timetableType;
            // 实例化(初始化课表)
            timetable = new Timetables(option);
            shop_id = $("#course-shop-select").val();
            day = this.getFirstDayOfWeek();
            this.init_course(shop_id,day)
            Controller.refreshCourseHeader(day);
            Controller.api.course();
        },

        api: {
            course: function () {
                const self = this;
                $("#course-shop-select").on("change", function () {
                    shop_id = $("#course-shop-select").val();
                    self.getCourse(shop_id, day);
                });

                $(document).on("click", "#last_week_btn", function () {
                    offset = offset - 1;
                    day = Controller.getLastWeek(day);
                    Controller.refreshCourseHeader(day)
                    self.getCourse(shop_id, day);
                });

                $(document).on("click", "#now_week_btn", function () {
                    offset = 0;
                    day = Controller.getFirstDayOfWeek()
                    Controller.refreshCourseHeader(day)
                    self.getCourse(shop_id, day);
                });

                $(document).on("click", "#next_week_btn", function () {
                    offset = offset + 1;
                    day = Controller.getNextWeek(day);
                    Controller.refreshCourseHeader(day)
                    self.getCourse(shop_id, day);
                });

                $(document).on("click", "#course_add_btn", function () {
                    const url = "xilufitness/work/course/add";
                    Fast.api.open(url, $(this).data("original-title") || $(this).attr("title") || __('Add'), $(this).data() || {});
                });

                $(document).on("click", "#courses_add_btn", function () {
                    const url = "xilufitness/work/course/add?add_flag=1";
                    Fast.api.open(url, $(this).data("original-title") || $(this).attr("title") || __('一键排课'), $(this).data() || {});
                });

                $(document).on("click", "#course_refresh_btn", function () {
                    self.getCourse(shop_id, day);
                });

                $(document).on("click", ".course_edit_btn", function () {
                    const ids = $(this).attr('data-field-index');
                    const url = "xilufitness/work/course/edit/ids/" + ids;
                    Fast.api.open(url, $(this).data("original-title") || $(this).attr("title") || __('Edit'), $(this).data() || {});
                    $('#mymodal').modal('hide');
                });

                $(document).on("click", ".course_del_btn", function () {
                    const ids = $(this).attr('data-field-index');
                    var url = "xilufitness/work/course/del";
                    var options = {
                        url: url,
                        data: {
                            action: 'del',
                            ids: ids
                        }
                    };
                    Fast.api.ajax(options, function (data, ret) {
                        self.getCourse(shop_id, day);
                        $('#mymodal').modal('hide');
                        return false;
                    }, function (data, ret) {
                        Toastr.error("删除失败！");
                    });
                });

            },
            getCourse: function (shop_id,day) {
                Fast.api.ajax({
                    url: Config.moduleurl + '/xilufitness/work/schedule/get_data',
                    data: {
                        begin_date: day,
                        shop_id: shop_id
                    },
                }, function (data) {
                    option.timetables = data;
                    timetable.setOption(option)
                    course_list = data
                    return false;
                });
            }
        }
    };

    return Controller;
});
