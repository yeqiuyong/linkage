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
                        <h2><i class="glyphicon glyphicon-list-alt"></i> 注册用户增长表(周)</h2>

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
                            <input id="date-user-per-week" class="form-control" size="16" type="text" name="user-week" value="" readonly>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            <span class="input-group-addon" onclick="loadUserCountPerWeek()"><span class="glyphicon glyphicon-ok-sign blue"></span></span>
                        </div>
                        <input type="hidden" id="dtp_input2" value="" /><br/>
                    </div>

                    <br>
                    <br>

                    <div class="box-content">
                        <div id="user-per-week-chart" class="center" style="height:300px"></div>
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
{{ javascript_include('bower_components/flot/jquery.flot.pie.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.stack.js') }}
{{ javascript_include('bower_components/flot/jquery.flot.resize.js') }}
<!-- chart libraries end -->

<script type="text/javascript">

    function loadUserCountPerWeek(){
        var dateStr = $("#date-user-per-week").prop('value');
        var dateOffset = Date.parse(new Date()) / 1000;

        if(dateStr != null && dateStr != ''){
            dateOffset = (Date.parse(new Date(dateStr))) / 1000;
        }

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/index/usercountperweek') ?>",
            data: {'date_offset':dateOffset },
            success: function (countArr) {
                userCountPerWeek(countArr);
            }
        });
    }

    function userCountPerWeek(countarr){
        if ($("#user-per-week-chart").length) {
            var manufacutres = [], transporters = [];
            var myDates = [];
            var offset = countarr.offset;

            for (var i = 0; i < countarr.manufactureCntsPerWeek.length; i++) {
                manufacutres.push([countarr.manufactureCntsPerWeek[i].x, countarr.manufactureCntsPerWeek[i].y]);
            }

            for (var i = 0; i < countarr.transporterCntsPerWeek.length; i++) {
                transporters.push([countarr.transporterCntsPerWeek[i].x, countarr.transporterCntsPerWeek[i].y]);
            }

            for(var i =1 ; i< 8; i++){
                var myDate = [i, getDateStr(offset, i)];
                myDates.push(myDate);
            }

            var plot = $.plot($("#user-per-week-chart"),
                    [
                        { data: manufacutres, label: "Manufacture"},
                        { data: transporters, label: "Transporter" },

                    ], {
                        series: {
                            lines: { show: true  },
                            points: { show: true }
                        },

                        xaxis: { ticks: myDates, min: 1, max: 7 },
                        yaxis: { ticks: 5, min: 0 },
                        grid: { hoverable: true, clickable: true, backgroundColor: { colors: ["#fff", "#eee"] } },

                        colors: ["#539F2E", "#3C67A5"]
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
            $("#user-per-week-chart").bind("plothover", function (event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));

                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                                y = item.datapoint[1].toFixed(2);

                        showTooltip(item.pageX, item.pageY,
                                "当天" + item.series.label + "注册数量为：" + y);
                    }
                }
                else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });

        }
    }

    function getDateStr(offset, dateCnt) {
        var cnt = 7 - dateCnt;
        offset = (offset - cnt * 86400) * 1000;

        var newDate = new Date();
        newDate.setTime(offset);

        return newDate.toLocaleDateString();
    }

    function initDatePlugin(){
        $('.form_date').datetimepicker({
            language:  'fr',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
    }

    initDatePlugin();
    loadUserCountPerWeek();

</script>