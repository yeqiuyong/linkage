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
                <a href="#">Dashboard</a>
            </li>
        </ul>
    </div>
    <div class=" row">
        <div class="col-md-3 col-sm-3 col-xs-6">
            <a data-toggle="tooltip" title="6 new members." class="well top-block" href="#">
                <i class="glyphicon glyphicon-user blue"></i>

                <div>用户数</div>
                <div>{{ totalCnt }}</div>
                <span class="notification">{{ newTotalCnt }}</span>
            </a>
        </div>

        <div class="col-md-3 col-sm-3 col-xs-6">
            <a data-toggle="tooltip" title="4 new pro members." class="well top-block" href="#">
                <i class="glyphicon glyphicon-user green"></i>

                <div>厂商</div>
                <div>{{ manufactureCnt }}</div>
                <span class="notification green">{{ newManufactureCnt }}</span>
            </a>
        </div>

        <div class="col-md-3 col-sm-3 col-xs-6">
            <a data-toggle="tooltip" title="4 new sales." class="well top-block" href="#">
                <i class="glyphicon glyphicon-user yellow"></i>

                <div>运营商</div>
                <div>{{ transporterCnt }}</div>
                <span class="notification yellow">{{ newTransporterCnt }}</span>
            </a>
        </div>

        <div class="col-md-3 col-sm-3 col-xs-6">
            <a data-toggle="tooltip" title="12 new messages." class="well top-block" href="#">
                <i class="glyphicon glyphicon-user red"></i>

                <div>司机</div>
                <div>{{ driverCnt }}</div>
                <span class="notification red">{{ newDriverCnt }}</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="box col-md-12">
            <div class="box col-md-12">
                <div class="box-inner">
                    <div class="box-header well">
                        <h2><i class="glyphicon glyphicon-list-alt"></i> Chart with points</h2>

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
                        <div id="sincos" class="center" style="height:300px"></div>
                        <p id="hoverdata">Mouse position at (<span id="x">0</span>, <span id="y">0</span>). <span
                                    id="clickdata"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- content ends -->
</div><!--/#content.col-md-0-->
</div><!--/fluid-row-->


<!-- Ad ends -->

<hr>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Settings</h3>
            </div>
            <div class="modal-body">
                <p>Here settings can be configured...</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
                <a href="#" class="btn btn-primary" data-dismiss="modal">Save changes</a>
            </div>
        </div>
    </div>
</div>


<!-- chart libraries start -->
{{ javascript_include('bower_components/flot/excanvas.min.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.js') }}
{{  javascript_include('bower_components/flot/jquery.flot.pie.js') }}
{{  javascript_include('bower_components/flot/jquery.flot.stack.js') }}
{{  javascript_include('bower_components/flot/jquery.flot.resize.js') }}
<!-- chart libraries end -->

<script type="text/javascript">
    if ($("#sincos").length) {
        var sin = [], cos = [], test=[];

        for (var i = 0; i < 15; i += 0.5) {
            sin.push([i, Math.sin(i) / i]);
            cos.push([i, Math.cos(i)]);
            test.push([i, Math.sin(i)]);
        }

        var plot = $.plot($("#sincos"),
                [
                    { data: sin, label: "sin(x)/x"},
                    { data: cos, label: "cos(x)" },
                    { data: test, label: "sin(x)" }
                ], {
                    series: {
                        lines: { show: true  },
                        points: { show: true }
                    },
                    grid: { hoverable: true, clickable: true, backgroundColor: { colors: ["#fff", "#eee"] } },
                    yaxis: { min: -1.2, max: 1.2 },
                    colors: ["#539F2E", "#3C67A5", "#3C27A5"]
                });

        function showTooltip(x, y, contents) {
            $('<div id="tooltip">' + contents + '</div>').css({
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 5,
                border: '1px solid #fdd',
                padding: '2px',
                'background-color': '#dfeffc',
                opacity: 0.80
            }).appendTo("body").fadeIn(200);
        }

        var previousPoint = null;
        $("#sincos").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));

            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2);

                    showTooltip(item.pageX, item.pageY,
                            item.series.label + " of " + x + " = " + y);
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;
            }
        });


        $("#sincos").bind("plotclick", function (event, pos, item) {
            if (item) {
                $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
                plot.highlight(item.series, item.datapoint);
            }
        });
    }
</script>