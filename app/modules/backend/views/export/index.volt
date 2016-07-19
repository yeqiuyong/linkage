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
                <a href="#">公司管理</a>
            </li>
        </ul>
    </div>


    <div class="box-content">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#manufacture" onclick="loadManufactures(1)">厂商</a></li>
            <li><a href="#transporter" onclick="loadTransporters(1)">承运商</a></li>
        </ul>

        <div class="tab-pane" id="adv-add">
            <div class="box col-md-10" >

                {{ form('admin/export/find', 'role': 'form', 'enctype':'multipart/form-data') }}

                    <div class="col-md-5">
                        <label>开始时间</label>
                        <div class="input-group date form_date " data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                           <input id="start_time" class="form-control" size="16" type="text" name="start_time" value="" readonly>
                           <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label>结束时间</label>
                        <div class="input-group date form_date " data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                           <input id="end_time" class="form-control" size="16" type="text" name="end_time" value="" readonly>
                           <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>
                    <div class="col-md-2" style="padding-left:40px;padding-top:25px;">
                        <button id="select" type="button" class="btn btn-success" onclick="">查询</button>
                    </div>
                </form>
            </div>

            <div class="box col-md-2" style="padding-top:25px;">
                <a href="" id="export"><button type="button" class="btn btn-primary">导出报表</button></a>
            </div>
        </div>

        <div id="myTabContent" class="tab-content">
            <div class="tab-pane active" id="manufacture">
                <div class="box col-md-12" >
                    <div class="box-inner">
                        <div class="box-header well" data-original-title="厂商信息">
                            <h2><i class="glyphicon glyphicon-list-alt"></i> 导出厂商列表</h2>
                            <div class="box-icon">
                                <a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
                                <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                            class="glyphicon glyphicon-chevron-up"></i></a>
                                <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
                            </div>
                        </div>
                        <div id="manufacture-table" class="box-content">
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="transporter">
                <div class="box col-md-12" >
                    <div class="box-inner">
                        <div class="box-header well" data-original-title="承运商信息">
                            <h2><i class="glyphicon glyphicon-list-alt"></i> 导出承运商列表</h2>
                            <div class="box-icon">
                                <a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
                                <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                            class="glyphicon glyphicon-chevron-up"></i></a>
                                <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
                            </div>
                        </div>
                        <div id="transporter-table" class="box-content">
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

</div>

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

{{ javascript_include('js/data-util.js') }}
<!-- chart libraries end -->

<script type="text/javascript">
    var pagesize = 10;
    var pageindexinit = 1;

    function loadManufactures(pageindex) {
        start_time= $('#start_time').val();
        end_time= $('#end_time').val();
        $('#select').attr('onclick','loadManufactures(1)');
        $('#export').attr('href','createManufactures?start_time='+start_time+'&end_time='+end_time);

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/export/manufactures') ?>",
            data: {'pageindex':pageindex,'start_time':start_time,'end_time':end_time},
            success: function (page) {
                render("loadManufactures", "manufacture-table", pageindex, page,"manuorder");
            }
        });
    }

    function loadTransporters(pageindex) {
        start_time= $('#start_time').val();
        end_time= $('#end_time').val();
        $('#select').attr('onclick','loadTransporters(1)');
        $('#export').attr('href','createTransporters?start_time='+start_time+'&end_time='+end_time);

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/export/transporters') ?>",
            data: {'pageindex':pageindex,'start_time':start_time,'end_time':end_time},
            success: function (page) {
                render("loadTransporters", "transporter-table", pageindex, page,"transorder");
            }
        });
    }

    function render(func, mydiv,  pageindex, page,companyorder){
        var strtable = '<table class="table table-striped table-bordered bootstrap-datatable datatable responsive">';
        strtable += '<thead><tr> <th>编号</th>  <th>公司名</th> <th>联系人</th> <th>联系电话</th> <th>注册时间</th> <th>状态</th> <th>操作</th> </tr> </thead>';

        var register_time = new Date();
        for (var i = 0; i < page.items.length; i++) {
            var num = i + 1 ;
            var id = page.items[i].id;
            register_time.setTime((parseInt(page.items[i].create_time) ) * 1000);

            strtable += "<tr>";
            strtable += "<td>" + num + "</td>";
            strtable += "<td>" + page.items[i].name + "</td>";
            strtable += "<td>" + page.items[i].contact + "</td>";
            strtable += "<td>" + page.items[i].phone + "</td>";
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

            strtable += '<td class="center">';
            strtable += '<a class="btn btn-success" href="'+companyorder+'?id='+id+'">';
            strtable += '<i class="glyphicon glyphicon-zoom-in icon-white"></i>';
            strtable += '查看订单';
            strtable += '</a>';
            strtable += '</td>';
            strtable += "</tr>";
        }

        strtable += '</table>';

        strtable += '<ul class="pagination pagination-centered">';
        strtable += '<li><a href="#" onclick="'+ func +'('+page.before+')">Prev</a></li>';

        for (var i = 0; i < page.total_pages; i++) {
            var index  = i + 1;
            if(index == pageindex){
                strtable += '<li class="active"><a href="#" onclick="'+ func +'('+index+')">'+index+'</a></li>';
            }else{
                strtable += '<li><a href="#" onclick="'+ func +'('+index+')">'+index+'</a></li>';
            }
        }

        strtable += '<li><a href="#" onclick="'+ func +'('+page.next+')">Next</a></li>';
        strtable +='</ul>';

        $("#"+mydiv).html(strtable);
    }

    function loadCompanyInfo(id){
        window.location.href = "<?php echo $this->url->get('admin/company/detail').'?id="+ id +"' ?>";
    }

    function changeStatus(id, status, func, pageindex){
        if(status == 4){
            if(!confirm("确定要删除该公司和其下所有用户？")){
                return;
            }
        }

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/company/changestatus') ?>",
            data: "id="+id+"&status="+status,
            success: function () {
                func(pageindex);
            }
        });

    }
    initDatePlugin();
    loadManufactures(pageindexinit);
</script>