{% include "contact/information.volt" %}


<div id="content" class="col-lg-10 col-sm-10">
    <!-- content starts -->
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a>
            </li>
            <li>
                <a href="#">投诉建议管理</a>
            </li>
        </ul>
    </div>


    <div class="box-content">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#adv-info">投诉建议列表</a></li>
        </ul>

        <div id="myTabContent" class="tab-content">
            <div class="tab-pane active" id="adv-info">
                <div class="box col-md-12" >
                    <div class="box-inner">
                        <div class="box-header well" data-original-title="管理员信息">
                            <div class="box-icon">
                                <a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
                                <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                            class="glyphicon glyphicon-chevron-up"></i></a>
                                <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
                            </div>
                        </div>
                        <div id="msg-table" class="box-content">
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

    function load(pageindex) {

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/contact/list') ?>",
            data: {'pageindex':pageindex},
            success: function (page) {

                var strtable = '<table class="table table-striped table-bordered bootstrap-datatable datatable responsive">';
                strtable += '<thead><tr><th>姓名</th> <th>电话</th> <th>邮箱</th> <th>描述</th> <th>创建时间</th> <th>状态</th> <th>操作</th> </tr> </thead>';

                var publish_time = new Date();
                for (var i = 0; i < page.items.length; i++) {
                    var id = page.items[i].id;
                    publish_time.setTime((parseInt(page.items[i].create_time) ) * 1000);

                    strtable += "<tr>";
                    strtable += "<td>" + page.items[i].name + "</td>";
                    strtable += "<td>" + page.items[i].telephone + "</td>";
                    strtable += "<td>" + page.items[i].email + "</td>";
                    strtable += "<td>" + page.items[i].comments + "</td>";
                    strtable += "<td>" + publish_time.toLocaleDateString() + "</td>";

                    strtable += '<td class="center">';
                    if(page.items[i].status == '0') {
                        strtable += '<span class="label-success label label-default">待处理</span>';
                    }else{
                        strtable += '<span class="label label-default">已处理</span>';
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
                    strtable += '<li><a href="#" onclick="changeStatus('+ id + ', 0' + ' ,' +  pageindex  + ')">待处理</a></li>';
                    strtable += '<li><a href="#" onclick="changeStatus('+ id + ', 1' + ' ,' +  pageindex  + ')">已处理</a></li>';
                    strtable += '<li><a href="#" onclick="changeStatus('+ id + ', 2' + ' ,' +  pageindex  + ')">删除</a></li>';
                    strtable += '</ul>';
                    strtable += '</div>';
                    strtable += '</td>';

                    strtable += "</tr>";
                }

                strtable += '</table>';

                strtable += '<ul class="pagination pagination-centered">';
                strtable += '<li><a href="#" onclick="load('+page.before+')">Prev</a></li>';

                for (var i = 0; i < page.total_pages; i++) {
                    var index  = i + 1;
                    if(index == pageindex){
                        strtable += '<li class="active"><a href="#" onclick="load('+index+')">'+index+'</a></li>';
                    }else{
                        strtable += '<li><a href="#" onclick="load('+index+')">'+index+'</a></li>';
                    }
                }

                strtable += '<li><a href="#" onclick="load('+page.next+')">Next</a></li>';
                strtable +='</ul>';

                $("#msg-table").html(strtable);
            }
        });
    }

    function changeStatus(id, status, pageindex){
        if(status == '2'){
            if(!confirm("确定要删除消息")){
                return;
            }
        }

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/contact/changestatus') ?>",
            data: "id="+id+"&status="+status+"&pageindex="+pageindex,
            success: function () {
                load(pageindex);
            }
        });

    }

    function showInfoModal(id){
        $('#info-modal').modal('show').on('shown',function(){
            $.ajax({
                type: "post",
                dataType:"json",
                url: "<?php echo $this->url->get('admin/contact/detail?id=" + id +"') ?>",
                data: {'id' : id},
                success: function (contact) {
                    var publish_time = new Date();
                    publish_time.setTime((parseInt(contact.create_time) ) * 1000);

                    $("#name-info-modal").attr("value", contact.name);//填充内容
                    $("#telephone-info-modal").attr("value", contact.telephone);//填充内容
                    $("#pub-time-info-modal").attr("value", publish_time.toDateString());//填充内容
                    $("#email-info-modal").attr("value", contact.email);//填充内容
                    $("#comments-info-modal").attr("value", contact.comments);//填充内容

                }
            });
        })

    }

    load(pageindexinit);
</script>