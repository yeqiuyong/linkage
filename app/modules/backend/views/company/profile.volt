<!-- content starts -->

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


    <div class="box col-md-8">
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

            <div class="box-content">
                <div id="order-per-mon" class="center" style="height:300px;"></div>
            </div>
        </div>
    </div>


    <div class=" row">
        <div class="box col-md-12" >
            <div class="box-inner">
                <div class="box-header well" data-original-title="管理员信息">
                    <h2><i class="glyphicon glyphicon-list-alt"></i> 员工信息表</h2>
                    <div class="box-icon">
                        <a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                    class="glyphicon glyphicon-chevron-up"></i></a>
                        <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
                    </div>
                </div>
                <div id="staff-table" class="box-content">
                </div>
            </div>
        </div>
    </div>

</div>


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

    function initChartOrderCountByType(countarr){
        //pie chart
        var orderCounts = [
            { label: "出口订单", data: countarr.exportCnt },
            { label: "进口订单", data: countarr.importCnt },
            { label: "自备柜", data: countarr.selfCnt }
        ];

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
    }


    function initChartOrderCountByMon(countarr){
        //stack chart
        if ($("#order-per-mon").length) {
            var data = [];
            var offset = countarr.offset;

            var d4OrderCount = [];
            for (var i = 0; i < countarr.count_group.length; i++) {
                d4OrderCount.push([countarr.count_group[i].order_date, countarr.count_group[i].order_num]);
            }
            data.push(d4OrderCount);

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

    function initStaffTable(companyId, pageindex, page){
        var strtable = '<table class="table table-striped table-bordered bootstrap-datatable datatable responsive">';
        strtable += '<thead><tr> <th>编号</th> <th>角色</th>  <th>用户名</th> <th>姓名</th> <th>电话</th> <th>注册时间</th> <th>状态</th> </tr> </thead>';

        var register_time = new Date();
        for (var i = 0; i < page.items.length; i++) {
            var num = i + 1 ;
            register_time.setTime((parseInt(page.items[i].create_time) ) * 1000);

            strtable += "<tr>";
            strtable += "<td>" + num + "</td>";
            strtable += "<td>" + page.items[i].user_id + "</td>";
            strtable += "<td>" + page.items[i].username + "</td>";
            strtable += "<td>" + page.items[i].name + "</td>";
            strtable += "<td>" + page.items[i].mobile + "</td>";
            strtable += "<td>" + register_time.toLocaleDateString() + "</td>";


            strtable += '<td class="center">';
            switch (page.items[i].status) {
                case '0': strtable += '<span class="label-success label label-default">Active</span>'; break;
                case '1': strtable += '<span class="label-default label">Inactive</span>'; break;
                case '2': strtable += '<span class="label-warning label">Pending</span>'; break;
                case '3': strtable += '<span class="label-default label-danger">Banned</span>'; break;
                default : strtable += '<span class="label-success label label-default">Active</span>';
            }
            strtable += '</td>';
            strtable += "</tr>";
        }

        strtable += '</table>';

        strtable += '<ul class="pagination pagination-centered">';
        strtable += '<li><a href="#" onclick="loadStaffs('+companyId+', '+page.before+')">Prev</a></li>';

        for (var i = 0; i < page.total_pages; i++) {
            var index  = i + 1;
            if(index == pageindex){
                strtable += '<li class="active"><a href="#" onclick="loadStaffs('+companyId+', '+index+')">'+index+'</a></li>';
            }else{
                strtable += '<li><a href="#" onclick="loadStaffs('+companyId+', '+index+')">'+index+'</a></li>';
            }
        }

        strtable += '<li><a href="#" onclick="loadStaffs('+companyId+', '+page.next+')">Next</a></li>';
        strtable +='</ul>';

        $("#staff-table").html(strtable);
    }

    function loadOrderCountByType(companyId){
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/order/ordercnt4companybytype') ?>",
            data: {'company_id':companyId },
            success: function (countArr) {
                initChartOrderCountByType(countArr);
            }
        });
    }

    function loadOrderCountByMon(companyId){
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/order/ordercnt4companybymon') ?>",
            data: {'company_id':companyId },
            success: function (countArr) {
                initChartOrderCountByMon(countArr);
            }
        });
    }

    function loadStaffs(companyId, pageindex){
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/clientuser/staffs') ?>",
            data: {'pageindex':pageindex, 'company_id':companyId},
            success: function (page) {
                initStaffTable(companyId, pageindex, page);
            }
        });
    }

    function initProfileNav(companyId){
        //initDatePlugin();
        loadOrderCountByType(companyId);
        loadOrderCountByMon(companyId);
        loadStaffs(companyId, 1);
    }


</script>