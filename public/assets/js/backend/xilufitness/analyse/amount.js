define(['jquery', 'bootstrap', 'backend', 'form', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend,Form, Datatable, Table, Echarts, undefined, Template) {
    var currentDate = Moment().startOf('month').format('YYYY-MM-DD HH:mm:ss');
    var viewType = 'day';
    var myChart;
    var Controller = {
        getData: function () {
            const dateRange = $(".datetimerange").val();
            Fast.api.ajax({
                url: Config.moduleurl + '/xilufitness/analyse/amount/get_data',
                data: {
                    datetime: dateRange,
                    viewType: viewType
                }
            }, function (data) {
                console.log('data',data);
                Controller.api.charts(data,'财务统计');
                return false;
            });
        },
        index: function () {

            $(window).resize(function () {
                myChart.resize();
            });

            $(document).on("click", ".btn-refresh", function () {
                Controller.getData();
            });

            //点击时间 今天 明天
            $(document).on("click", ".btn-filter", function () {
                const label = $(this).text();
                const obj = $(".datetimerange").data("daterangepicker");
                const dates = obj.ranges[label];
                obj.startDate = dates[0];
                obj.endDate = dates[1];
                obj.clickApply();
            });

            $(document).on("click", ".day-view", function () {
                $("#month-btn-group").show();
                $("#year-btn-group").hide()
                $(".select-view").removeClass('btn-success');
                $(".select-view").addClass('btn-default');
                $(this).removeClass('btn-default');
                $(this).addClass('btn-success');
                viewType = 'day';
                const obj = $(".datetimerange").data("daterangepicker");
                obj.startDate = Moment().startOf('month');
                obj.endDate = Moment().endOf('month');
                currentDate = obj.startDate.format('YYYY-MM-DD HH:mm:ss');
                obj.clickApply();
                Controller.getData();
            });

            $(document).on("click", ".week-view", function () {
                $("#month-btn-group").show();
                $("#year-btn-group").hide()
                $(".select-view").removeClass('btn-success');
                $(".select-view").addClass('btn-default');
                $(this).removeClass('btn-default');
                $(this).addClass('btn-success');
                viewType = 'week';
                const obj = $(".datetimerange").data("daterangepicker");
                obj.startDate = Moment().startOf('month');
                obj.endDate = Moment().endOf('month');
                currentDate = obj.startDate.format('YYYY-MM-DD HH:mm:ss');
                obj.clickApply();
                Controller.getData();
            });

            $(document).on("click", ".month-view", function () {
                $("#month-btn-group").hide();
                $("#year-btn-group").show();
                $(".select-view").removeClass('btn-success');
                $(".select-view").addClass('btn-default');
                $(this).removeClass('btn-default');
                $(this).addClass('btn-success');
                viewType = 'month';
                const obj = $(".datetimerange").data("daterangepicker");
                obj.startDate = Moment().startOf('year');
                obj.endDate = Moment().endOf('year');
                currentDate = obj.startDate.format('YYYY-MM-DD HH:mm:ss');
                obj.clickApply();
                Controller.getData();
            });

            $(document).on("click", "#last_month_btn", function () {
                console.log(currentDate);
                var cur = Moment(currentDate, "YYYY-MM-DD hh:mm:ss").startOf('month');
                const obj = $(".datetimerange").data("daterangepicker");
                obj.startDate = Moment(cur).subtract(1, 'months');
                obj.endDate = Moment(obj.startDate).endOf('month');
                currentDate = obj.startDate.format('YYYY-MM-DD HH:mm:ss');
                obj.clickApply();
                Controller.getData();
            });

            $(document).on("click", "#now_month_btn", function () {
                const obj = $(".datetimerange").data("daterangepicker");
                obj.startDate = Moment().startOf('month');
                obj.endDate = Moment().endOf('month');
                currentDate = obj.startDate.format('YYYY-MM-DD HH:mm:ss');
                obj.clickApply();
                Controller.getData();
            });

            $(document).on("click", "#next_month_btn", function () {
                console.log(currentDate);
                var cur = Moment(currentDate, "YYYY-MM-DD hh:mm:ss").startOf('month');
                const obj = $(".datetimerange").data("daterangepicker");
                obj.startDate = Moment(cur).add(1, 'months');
                obj.endDate = Moment(obj.startDate).endOf('month');
                currentDate = obj.startDate.format('YYYY-MM-DD HH:mm:ss');
                obj.clickApply();
                Controller.getData();
            });

            $(document).on("click", "#last_year_btn", function () {
                console.log(currentDate);
                var cur = Moment(currentDate, "YYYY-MM-DD hh:mm:ss").startOf('year');
                const obj = $(".datetimerange").data("daterangepicker");
                obj.startDate = Moment(cur).subtract(1, 'years');
                obj.endDate = Moment(obj.startDate).endOf('year');
                currentDate = obj.startDate.format('YYYY-MM-DD HH:mm:ss');
                obj.clickApply();
                Controller.getData();
            });

            $(document).on("click", "#now_year_btn", function () {
                const obj = $(".datetimerange").data("daterangepicker");
                obj.startDate = Moment().startOf('year');
                obj.endDate = Moment().endOf('year');
                currentDate = obj.startDate.format('YYYY-MM-DD HH:mm:ss');
                obj.clickApply();
                Controller.getData();
            });

            $(document).on("click", "#next_year_btn", function () {
                console.log(currentDate);
                var cur = Moment(currentDate, "YYYY-MM-DD hh:mm:ss").startOf('year');
                const obj = $(".datetimerange").data("daterangepicker");
                obj.startDate = Moment(cur).add(1, 'years');
                obj.endDate = Moment(obj.startDate).endOf('year');
                currentDate = obj.startDate.format('YYYY-MM-DD HH:mm:ss');
                obj.clickApply();
                Controller.getData();
            });


            Controller.api.forms();
        },
        api: {
            charts: function (data,title) {
                var series = [];
                var i = 0;
                for (var o in data.series) {
                    var element = data.series[o];
                    series.push({
                        name: data.fieldtextdata[i],
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: element,
                    });
                    i++;
                }
                myChart = Echarts.init(document.getElementById('echart_order'), 'walden');
                myChart.resize();
                // 指定图表的配置项和数据
                var option = {
                    title: {
                        text: title,
                        subtext: ''
                    },
                    tooltip: {
                        trigger: 'axis',
                    },
                    legend: {
                        data: data.fieldtextdata
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: data.column,
                    },
                    yAxis: {
                        boundaryGap: [0, '100%'],
                        type: 'value'
                    },
                    grid: [{
                        left: '3%',
                        top: '1%',
                        right: '3%',
                        bottom: '0',
                        containLabel: true
                    }],
                    series: series
                };
                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option,true);

                $(window).resize(function () {
                    myChart.resize();
                });

            },
            forms:function () {
                var form = $("form[role=form]");
                var events = Form.events;
                events.daterangepicker(form)
                events.datetimepicker(form);
                events.selectpage(form);
                Controller.getData("day");
            }
        }
    };

    return Controller;
});
