define(['jquery', 'bootstrap', 'backend', 'form', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend,Form, Datatable, Table, Echarts, undefined, Template) {
    var myChart;
    var Controller = {

        index: function () {

            $(window).resize(function () {
                myChart.resize();
            });

            $(document).on("click", ".btn-refresh", function () {
                setTimeout(function () {
                    myChart.resize();
                }, 0);
            });

            //触发数据刷新
            $(".datetimerange").on("blur", function () {
                Fast.api.ajax({
                    url: Config.moduleurl + '/xilufitness/analyse/index/get_data',
                    data: {datetime: $(this).val()}
                }, function (data) {
                    console.log('data',data);
                    Controller.api.charts(data,'订单统计');
                    return false;
                });
            });
            //刷新
            $(document).on("click", ".btn-refresh,li", function () {
                setTimeout(function () {
                    myChart.resize();
                    console.log('resize')
                }, 200);
            });
            //点击时间 今天 明天
            $(document).on("click", ".btn-filter", function () {
                var label = $(this).text();
                var obj = $(".datetimerange").data("daterangepicker");
                var dates = obj.ranges[label];
                obj.startDate = dates[0];
                obj.endDate = dates[1];
                obj.clickApply();
            });
            //点击刷新图表
            $(document).on("click", ".btn-refresh", function () {
                $(".datetimerange").trigger("blur");
            });
            $(".content").find('.datetimerange').each(function(){
                $(".datetimerange").trigger("blur");
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
                //点击时间 今天 明天
                $(document).on("click", ".btn-filter", function () {
                    var label = $(this).text();
                    var obj = $("#search_time").data("daterangepicker");
                    var dates = obj.ranges[label];
                    obj.startDate = dates[0];
                    obj.endDate = dates[1];
                    obj.clickApply();
                });
                var form = $("form[role=form]");
                var events = Form.events;
                events.daterangepicker(form)
                events.datetimepicker(form);
                events.selectpage(form);
                $(".btn-refresh").on("click",function () {
                    window.location.reload();
                });
            }
        }
    };

    return Controller;
});
