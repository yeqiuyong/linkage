{% include "clientuser/information.volt" %}

<div id="content" class="col-lg-10 col-sm-10">
    <!-- content starts -->
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">主页</a>
            </li>
            <li>
                <a href="#">用户管理</a>
            </li>
        </ul>
    </div>


    <div class="box-content">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#manufacture" onclick="loadManufactures(1)">厂商</a></li>
            <li><a href="#transporter" onclick="loadTransporters(1)">承运商</a></li>
            <li><a href="#driver" onclick="loadDrivers(1)">司机</a></li>
        </ul>

        <div id="myTabContent" class="tab-content">
            <div class="tab-pane active" id="manufacture">
                <div class="box col-md-12" >
                    <div class="box-inner">
                        <div class="box-header well" data-original-title="厂商信息">
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

            <div class="tab-pane" id="driver">
                <div class="box col-md-12" >
                    <div class="box-inner">
                        <div class="box-header well" data-original-title="司机信息">
                            <div class="box-icon">
                                <a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
                                <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                            class="glyphicon glyphicon-chevron-up"></i></a>
                                <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
                            </div>
                        </div>
                        <div id="driver-table" class="box-content">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Ad ends -->

<script type="text/javascript">
    var pagesize = 10;
    var pageindexinit = 1;

    function loadManufactures(pageindex) {
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/clientuser/manufactures') ?>",
            data: {'pageindex':pageindex},
            success: function (page) {
                render("loadManufactures", "manufacture-table", pageindex, page);
            }
        });
    }

    function loadTransporters(pageindex) {
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/clientuser/transporters') ?>",
            data: {'pageindex':pageindex},
            success: function (page) {
                render("loadTransporters", "transporter-table", pageindex, page);
            }
        });
    }

    function loadDrivers(pageindex){
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/clientuser/drivers') ?>",
            data: {'pageindex':pageindex},
            success: function (page) {
                render("loadDrivers", "driver-table", pageindex, page);
            }
        });
    }

    function render(func, mydiv,  pageindex, page){
        //var myfunc = func.substring(4);
        var strtable = '<table class="table table-striped table-bordered bootstrap-datatable datatable responsive">';
        strtable += '<thead><tr> <th>编号</th> <th>角色</th>  <th>用户名</th> <th>电话</th> <th>注册时间</th> <th>状态</th> <th>操作</th> </tr> </thead>';

        var register_time = new Date();
        for (var i = 0; i < page.items.length; i++) {
            var num = i + 1 ;
            var id = page.items[i].user_id;

            register_time.setTime((parseInt(page.items[i].create_time) ) * 1000);

            strtable += "<tr>";
            strtable += "<td>" + num + "</td>";
            strtable += "<td>" + page.items[i].rolename + "</td>";
            strtable += "<td>" + page.items[i].username + "</td>";
            strtable += "<td>" + page.items[i].mobile + "</td>";
            strtable += "<td>" + register_time.toLocaleDateString() + "</td>";


            strtable += '<td class="center">';
            switch (page.items[i].status) {
                case '0': strtable += '<span class="label-success label label-default">Active</span>'; break;
                case '1': strtable += '<span class="label-default label">Inactive</span>'; break;
                case '2': strtable += '<span class="label-warning label">Pending</span>'; break;
                case '3': strtable += '<span class="label-default label-danger">Banned</span>'; break;
                case '4': strtable += '<span class="label-default label-danger">Delete</span>'; break;
                default : strtable += '<span class="label-success label label-default">Active</span>';
            }
            strtable += '</td>';

            strtable += '<td class="center">';
            strtable += '<a class="btn btn-success" href="#" onclick="showInfoModal('+ id +')">';
            strtable += '<i class="glyphicon glyphicon-zoom-in icon-white"></i>';
            strtable += '查看';
            strtable += '</a>';
            strtable += '<div class="btn-group">';
            strtable += '<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">';
            strtable += '<i class="glyphicon glyphicon-edit"></i><span class="hidden-sm hidden-xs"> 状态</span>';
            strtable += '<span class="caret"></span>';
            strtable += '</button>';
            strtable += '<ul class="dropdown-menu">';
            strtable += '<li class="divider"></li>';
            strtable += '<li><a href="#" onclick="changeStatus('+ id + ', 0' + ', ' + func + ' ,' +  pageindex  + ')">Active</a></li>';
            strtable += '<li><a href="#" onclick="changeStatus('+ id + ', 1' + ', ' + func + ' ,' +  pageindex  + ')">Inactive</a></li>';
            strtable += '<li><a href="#" onclick="changeStatus('+ id + ', 3' + ', ' + func + ' ,' +  pageindex  + ')">Banned</a></li>';
            strtable += '<li><a href="#" onclick="changeStatus('+ id + ', 4' + ', ' + func + ' ,' +  pageindex  + ')">Delete</a></li>';
            strtable += '</ul>';
            strtable += '</div>';
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

    function loadUser(id){
        window.location.href = "<?php echo $this->url->get('admin/clientuser/information').'?id="+ id +"' ?>";
    }

    function changeStatus(id, status, func, pageindex){
        if(status == 4){
            if(!confirm("确定要删除该用户？")){
                return;
            }
        }

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/clientuser/changestatus') ?>",
            data: "id="+id+"&status="+status+"&pageindex="+pageindex,
            success: function () {
                func(pageindex);
            }
        });

    }

    function showInfoModal(id){
        $('#info-modal').modal('show').on('shown',function(){
            $.ajax({
                type: "post",
                dataType:"json",
                url: "<?php echo $this->url->get('admin/clientuser/detail') ?>",
                data: {'id' : id},
                success: function (clientuser) {
                    var update_time = new Date();
                    update_time.setTime((parseInt(clientuser.update_time) ) * 1000);

                    $("#username-info-modal").attr("value", clientuser.username);//填充内容
                    $("#realname-info-modal").attr("value", clientuser.realname);//填充内容
                    $("#mobile-info-modal").attr("value", clientuser.mobile);//填充内容
                    $("#email-info-modal").attr("value", clientuser.email);//填充内容
                    $("#role-info-modal").attr("value", clientuser.role);//填充内容
                    $("#update-info-modal").attr('value', update_time.toDateString());
                }
            });
        })

    }

    loadManufactures(pageindexinit);
</script>