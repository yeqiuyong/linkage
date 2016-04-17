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
                <a href="#">出口订单</a>
            </li>
        </ul>
    </div>

    <div class=" row">

        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-list-alt"></i> 出口订单月报表</h2>

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

    <div class=" row">
        <div class="box col-md-12" >
            <div class="box-inner">
                <div class="box-header well" data-original-title="管理员信息">
                    <h2><i class="glyphicon glyphicon-list-alt"></i> 厂商出口订单纪录表</h2>
                    <div class="box-icon">
                        <a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                    class="glyphicon glyphicon-chevron-up"></i></a>
                        <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
                    </div>
                </div>
                <div id="manufacture-order-table" class="box-content">
                </div>
            </div>
        </div>
    </div>

    <div class=" row">
        <div class="box col-md-12" >
            <div class="box-inner">
                <div class="box-header well" data-original-title="管理员信息">
                    <h2><i class="glyphicon glyphicon-list-alt"></i> 承运商出口订单纪录表</h2>
                    <div class="box-icon">
                        <a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                    class="glyphicon glyphicon-chevron-up"></i></a>
                        <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
                    </div>
                </div>
                <div id="transporter-order-table" class="box-content">
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
    var pagesize = 10;
    var pageindexinit = 1;

    function initChartOrderCountByMon(countarr){
        if ($("#order-per-mon").length) {
            var data = [];
            var offset = countarr.offset;

            var d4Export = [];
            for (var i = 0; i < countarr.export.length; i++) {
                d4Export.push([countarr.export[i].order_date, countarr.export[i].order_num]);
            }
            data.push(d4Export);

            var xaxis = initChartXaxis4Mon(offset);

            function plotWithOptions() {
                $.plot($("#order-per-mon"), data, {
                    series: {
                        stack: 0,
                        bars: { show: true, barWidth:0.5, align:'center',multiplebars:true}
                    },
                    xaxis: { ticks: xaxis, min: 1, max: 12 },
                    colors: ["#DC5625"]
                });
            }

            plotWithOptions();

        }
    }

    function initOrderCountTable(page, pageindex, orderType, orderSubType, tableTag){
        var strtable = '<table class="table table-striped table-bordered bootstrap-datatable datatable responsive">';
        strtable += '<thead><tr> <th>公司名</th> <th>注册时间</th> <th>'+ orderType +'</th> <th>'+ orderSubType +'</th> </tr> </thead>';

        var register_time = new Date();
        for (var i = 0; i < page.items.length; i++) {
            register_time.setTime((parseInt(page.items[i].create_time) ) * 1000);

            strtable += "<tr>";
            strtable += "<td>" + page.items[i].company_name + "</td>";
            strtable += "<td>" + register_time.toDateString() + "</td>";
            strtable += "<td>" + page.items[i].order_num + "</td>";
            strtable += "<td>" + page.items[i].sub_order_num + "</td>";
            strtable += "</tr>";
        }

        strtable += '</table>';

        strtable += '<ul class="pagination pagination-centered">';
        strtable += '<li><a href="#" onclick="loadManufacureOrderTable('+page.before+','+ orderType +','+ orderSubType +','+tableTag+')">Prev</a></li>';

        for (var i = 0; i < page.total_pages; i++) {
            var index  = i + 1;
            if(index == pageindex){
                strtable += '<li class="active"><a href="#" onclick="loadManufacureOrderTable('+index+','+ orderType +','+ orderSubType +','+tableTag+')">'+index+'</a></li>';
            }else{
                strtable += '<li><a href="#" onclick="loadManufacureOrderTable('+index+','+ orderType +','+ orderSubType +','+tableTag+')">'+index+'</a></li>';
            }
        }

        strtable += '<li><a href="#" onclick="loadManufacureOrderTable('+page.before+','+ orderType +','+ orderSubType +','+tableTag+')">Next</a></li>';
        strtable +='</ul>';

        $("#" + tableTag).html(strtable);
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

    function loadManufacureOrderTable(pageindex, orderType, orderSubType, tableTag) {
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/order/getmanufactureorderlist') ?>",
            data: {'pageindex':pageindex, 'order_type':0},
            success: function (page) {
                initOrderCountTable(page, pageindex, orderType, orderSubType, tableTag)
            }
        });
    }

    function loadTransporterOrderTable(pageindex, orderType, orderSubType, tableTag) {
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/order/gettransporterorderlist') ?>",
            data: {'pageindex':pageindex, 'order_type':0},
            success: function (page) {
                initOrderCountTable(page, pageindex, orderType, orderSubType, tableTag)
            }
        });
    }

    initDatePlugin();
    loadOrderCountByMon();
    loadManufacureOrderTable(pageindexinit, '厂商订单数','厂商出口订单数', 'manufacture-order-table');
    loadTransporterOrderTable(pageindexinit, '承运商订单数','承运商出口订单数', 'transporter-order-table');

</script>