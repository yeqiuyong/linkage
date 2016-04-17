<noscript>
    <div class="alert alert-block col-md-12">
        <h4 class="alert-heading">Warning!</h4>

        <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a>
            enabled to use this site.</p>
    </div>
</noscript>

<div id="content" class="col-lg-10 col-sm-10">
    <!-- content starts -->
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a>
            </li>
            <li>
                <a href="#">订单</a>
            </li>
        </ul>
    </div>

    <div class=" row">
        <div class="col-md-3 col-sm-3 col-xs-6">
            <a data-toggle="tooltip" title="6 new members." class="well top-block" href="#">
                <i class="glyphicon glyphicon-shopping-cart blue"></i>

                <div>订单数</div>
                <div>{{ totalCnt }}</div>
            </a>
        </div>

        <div class="col-md-3 col-sm-3 col-xs-6">
            <a data-toggle="tooltip" title="4 new pro members." class="well top-block" href="#">
                <i class="glyphicon glyphicon-shopping-cart green"></i>

                <div>出口订单数</div>
                <div>{{ exportCnt }}</div>
            </a>
        </div>

        <div class="col-md-3 col-sm-3 col-xs-6">
            <a data-toggle="tooltip" title="4 new sales." class="well top-block" href="#">
                <i class="glyphicon glyphicon-shopping-cart yellow"></i>

                <div>进口订单数</div>
                <div>{{ importCnt }}</div>
            </a>
        </div>

        <div class="col-md-3 col-sm-3 col-xs-6">
            <a data-toggle="tooltip" title="12 new messages." class="well top-block" href="#">
                <i class="glyphicon glyphicon-shopping-cart red"></i>

                <div>自备柜订单数</div>
                <div>{{ selfCnt }}</div>
            </a>
        </div>
    </div>

    <div class=" row">

        <div class="box col-md-4">
            <div class="box-inner">
                <div class="box-header well" data-original-title="">
                    <h2><i class="glyphicon glyphicon-list-alt"></i> 订单占比图</h2>

                    <div class="box-icon">
                        <a href="#" class="btn btn-setting btn-round btn-default"><i
                                    class="glyphicon glyphicon-cog"></i></a>
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                    class="glyphicon glyphicon-chevron-up"></i></a>
                        <a href="#" class="btn btn-close btn-round btn-default"><i
                                    class="glyphicon glyphicon-remove"></i></a>
                    </div>
                </div>
                <div class="box-content">
                    <div id="order-chart" style="height:300px"></div>
                </div>
            </div>
        </div>


        <div class="box col-md-4">
            <div class="box-inner">
                <div class="box-header well" data-original-title="">
                    <h2><i class="glyphicon glyphicon-list-alt"></i> 厂商下单占比图</h2>

                    <div class="box-icon">
                        <a href="#" class="btn btn-setting btn-round btn-default"><i
                                    class="glyphicon glyphicon-cog"></i></a>
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                    class="glyphicon glyphicon-chevron-up"></i></a>
                        <a href="#" class="btn btn-close btn-round btn-default"><i
                                    class="glyphicon glyphicon-remove"></i></a>
                    </div>
                </div>
                <div class="box-content">
                    <div id="donutchart" style="height: 300px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="box col-md-4">
            <div class="box-inner">
                <div class="box-header well" data-original-title="">
                    <h2><i class="glyphicon glyphicon-list-alt"></i> 承运商接单占比图</h2>

                    <div class="box-icon">
                        <a href="#" class="btn btn-setting btn-round btn-default"><i
                                    class="glyphicon glyphicon-cog"></i></a>
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                    class="glyphicon glyphicon-chevron-up"></i></a>
                        <a href="#" class="btn btn-close btn-round btn-default"><i
                                    class="glyphicon glyphicon-remove"></i></a>
                    </div>
                </div>
                <div class="box-content">
                    <div id="accept-chart" style="height: 300px;">
                    </div>
                </div>
            </div>
        </div>


        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-list-alt"></i> 订单月报表</h2>

                    <div class="box-icon">
                        <a href="#" class="btn btn-setting btn-round btn-default"><i
                                    class="glyphicon glyphicon-cog"></i></a>
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                    class="glyphicon glyphicon-chevron-up"></i></a>
                        <a href="#" class="btn btn-close btn-round btn-default"><i
                                    class="glyphicon glyphicon-remove"></i></a>
                    </div>
                </div>

                <br>

                <div class="control-group col-xs-4 col-md-4 col-xs-offset-8">
                    <div class="input-group date form_date " data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                        <input id="date-order-per-mon" class="form-control" size="16" type="text" name="user-mon" value="" readonly>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        <span class="input-group-addon" onclick="loadOrderCountByMon()"><span class="glyphicon glyphicon-ok-sign blue"></span></span>
                    </div>
                    <input type="hidden" id="dtp_input2" value="" /><br/>
                </div>

                <br>
                <br>

                <div class="box-content">
                    <div id="order-per-mon" class="center" style="height:300px;"></div>
                </div>
            </div>
        </div>


    </div>
</div><!--/fluid-row-->


<!-- Ad ends -->

<!-- chart libraries start -->
{{ javascript_include('bower_components/flot/excanvas.min.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.pie.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.stack.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.resize.js') }}

{{ javascript_include('js/data-util.js') }}
<!-- chart libraries end -->

<script type="text/javascript">

    //pie chart
    var orderCounts = [
        { label: "出口订单", data: {{ exportCnt }} },
        { label: "进口订单", data: {{ importCnt }} },
        { label: "自备柜", data: {{ selfCnt }} }
    ];

    var placeOrderCounts = [];
    {% for placeOrderCount in placeOrderCounts %}
        placeOrderCounts.push({ label: "{{ placeOrderCount['company_name'] }}", data: {{ placeOrderCount['order_num'] }} });
    {% endfor %}

    var acceptOrderCounts = [];
    {% for acceptOrderCount in acceptOrderCounts %}
    acceptOrderCounts.push({ label: "{{ acceptOrderCount['company_name'] }}", data: {{ acceptOrderCount['order_num'] }} });
    {% endfor %}



    if ($("#order-chart").length) {
        $.plot($("#order-chart"), orderCounts,
                {
                    series: {
                        pie: {
                            show: true
                        }
                    },
                    grid: {
                        hoverable: true,
                        clickable: true
                    },
                    legend: {
                        show: false
                    }
                });

        function pieHover(event, pos, obj) {
            if (!obj)
                return;
            percent = parseFloat(obj.series.percent).toFixed(2);
            $("#hover").html('<span style="font-weight: bold; color: ' + obj.series.color + '">' + obj.series.label + ' (' + percent + '%)</span>');
        }

        $("#order-chart").bind("plothover", pieHover);
    }



    //donut chart
    if ($("#donutchart").length) {
        $.plot($("#donutchart"), placeOrderCounts,
                {
                    series: {
                        pie: {
                            innerRadius: 0.5,
                            show: true
                        }
                    },
                    legend: {
                        show: false
                    }
                });
    }


    //donut chart2
    if ($("#accept-chart").length) {
        $.plot($("#accept-chart"), acceptOrderCounts,
                {
                    series: {
                        pie: {
                            innerRadius: 0.5,
                            show: true
                        }
                    },
                    legend: {
                        show: false
                    }
                });
    }

    function initChartOrderCountByMon(countarr){
        //stack chart
        if ($("#order-per-mon").length) {
            var data = [];
            var offset = countarr.offset;

            var d4Export = [];
            for (var i = 0; i < countarr.export.length; i++) {
                d4Export.push([countarr.export[i].order_date, 2]);
            }

            var d4Import = [];
            for (var i = 0; i < countarr.import.length; i++) {
                d4Import.push([countarr.import[i].order_date, 1]);
            }

            var d4Self = [];
            for (var i = 0; i < countarr.export.length; i++) {
                d4Self.push([countarr.self[i].order_date, 3]);
            }

            data.push(d4Export);
            data.push(d4Import);
            data.push(d4Self);

            var xaxis = initChartXaxis4Mon(offset);

            function plotWithOptions() {
                $.plot($("#order-per-mon"),  data, {
                    series: {
                        stack: 0,
                        bars: { show: true, barWidth:0.5, align:'center',multiplebars:true}
                    },
                    xaxis: { ticks: xaxis, min: 1, max: 12 },
                    colors: ["#DC5625", "#007ACC", "#99FF99"]

                });
            }

            plotWithOptions();
        }
    }

    function loadOrderCountByMon(){
        var dateStr = $("#date-order-per-mon").prop('value');
        var dateOffset = Date.parse(new Date()) / 1000;

        if(dateStr != null && dateStr != ''){
            dateOffset = (Date.parse(new Date(dateStr))) / 1000;
        }

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/order/ordercountpermon') ?>",
            data: {'date_offset':dateOffset },
            success: function (countArr) {
                initChartOrderCountByMon(countArr);
            }
        });
    }

    initDatePlugin();
    loadOrderCountByMon();

</script>