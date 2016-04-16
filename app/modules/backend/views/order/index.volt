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

    </div>
</div><!--/fluid-row-->


<!-- Ad ends -->

<!-- chart libraries start -->
{{ javascript_include('bower_components/flot/excanvas.min.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.pie.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.stack.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.resize.js') }}
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

</script>