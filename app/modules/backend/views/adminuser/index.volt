{% include "adminuser/information.volt" %}

<div id="content" class="col-lg-10 col-sm-10">
    <!-- content starts -->
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a>
            </li>
            <li>
                <a href="#">管理员管理</a>
            </li>
        </ul>
    </div>


    <div class="box-content">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#admin-info">个人信息</a></li>
            <li><a href="#admin-add">添加管理员</a></li>
        </ul>

        <div id="myTabContent" class="tab-content">
            <div class="tab-pane active" id="admin-info">
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
                        <div id="user-table" class="box-content">
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="admin-add">
                <div class="box col-md-12">
                    <div class="box-inner">
                        <div class="box-content">
                            {{ form('admin/adminuser/add', 'role': 'form') }}
                            <div class="form-group">
                                <label>用户名</label>
                                {{ text_field('username', 'class': "form-control") }}
                            </div>
                            <div class="form-group">
                                <label>密码</label>
                                {{ password_field('password', 'class': "form-control") }}
                            </div>
                            <div class="form-group">
                                <label>姓名</label>
                                {{ text_field('realname', 'class': "form-control") }}
                            </div>
                            <div class="form-group">
                                <label>电话</label>
                                {{ text_field('mobile', 'class': "form-control") }}
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">邮箱</label>
                                {{ text_field('email', 'class': "form-control") }}
                            </div>

                            {{ submit_button('提交', 'class': 'btn btn-primary') }}
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<script type="text/javascript">
    var pagesize = 10;
    var pageindexinit = 1;

    function load(pageindex) {

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/adminuser/list') ?>",
            data: {'pageindex':pageindex},
            success: function (page) {

                var strtable = '<table class="table table-striped table-bordered bootstrap-datatable datatable responsive">';
                strtable += '<thead><tr> <th>用户名</th> <th>注册时间</th> <th>角色</th> <th>状态</th> <th>操作</th> </tr> </thead>';

                var register_time = new Date();
                for (var i = 0; i < page.items.length; i++) {
                    var id = page.items[i].id;
                    register_time.setTime((parseInt(page.items[i].create_time) ) * 1000);

                    strtable += "<tr>";
                    strtable += "<td>" + page.items[i].username + "</td>";
                    strtable += "<td>" + register_time.toLocaleDateString() + "</td>";
                    strtable += "<td>" + page.items[i].profile_name + "</td>";

                    strtable += '<td class="center">';
                    if(page.items[i].status == '0') {
                        strtable += '<span class="label-success label label-default">Active</span>';
                    }else{
                        strtable += '<span class="label label-default">Inactive</span>';
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
                    strtable += '<li class="admin"></li>';
                    strtable += '<li><a href="#" onclick="changeStatus('+ id + ', 0' + ' ,' +  pageindex  + ')">Active</a></li>';
                    strtable += '<li><a href="#" onclick="changeStatus('+ id + ', 1' + ' ,' +  pageindex  + ')">Inactive</a></li>';
                    strtable += '<li><a href="#" onclick="changeStatus('+ id + ', 2' + ' ,' +  pageindex  + ')">Delete</a></li>';
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

                $("#user-table").html(strtable);
            }
        });
    }

    function changeStatus(id, status, pageindex){
        if(status == '2'){
            if(!confirm("确定要删除用户")){
                return;
            }
        }

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/adminuser/changestatus') ?>",
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
                url: "<?php echo $this->url->get('admin/adminuser/detail?id=" + id +"') ?>",
                data: {'id' : id},
                success: function (adminuser) {
                    var update_time = new Date();
                    update_time.setTime((parseInt(adminuser.update_time) ) * 1000);

                    $("#username-info-modal").attr("value", adminuser.username);//填充内容
                    $("#realname-info-modal").attr("value", adminuser.realname);//填充内容
                    $("#mobile-info-modal").attr("value", adminuser.mobile);//填充内容
                    $("#email-info-modal").attr("value", adminuser.email);//填充内容
                    $("#update-info-modal").attr('value', update_time.toDateString());
                }
            });
        })

    }

    load(pageindexinit);
</script>