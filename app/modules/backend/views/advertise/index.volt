
    {% include "advertise/editor.volt" %}


<div id="content" class="col-lg-10 col-sm-10">
    <!-- content starts -->
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="#">Home</a>
            </li>
            <li>
                <a href="#">广告管理</a>
            </li>
        </ul>
    </div>


    <div class="box-content">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#adv-info">广告信息</a></li>
            <li><a href="#adv-add">添加广告</a></li>
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
                        <div id="adv-table" class="box-content">
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="adv-add">
                <div class="box col-md-12">
                    <div class="box-inner">
                        <div class="box-content">
                            {{ form('admin/advertise/add', 'role': 'form', 'enctype':'multipart/form-data') }}
                            <div class="form-group">
                                <label>广告标题</label>
                                {{ text_field('title', 'class': "form-control") }}
                            </div>
                            <div class="form-group">
                                <label>广告链接</label>
                                {{ text_field('link', 'class': "form-control") }}
                            </div>
                            <div class="form-group">
                                <label>广告描述</label>
                                {{ text_field('description', 'class': "form-control") }}
                            </div>
                            <div class="form-group">
                                <label>其他说明</label>
                                {{ text_field('memo', 'class': "form-control") }}
                            </div>
                            <div class="form-group">
                                <label>图片</label>
                                {{ file_field('image', 'class': "form-control") }}
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

<!-- Ad ends -->


<script type="text/javascript">
    var pagesize = 10;
    var pageindexinit = 1;

    function load(pageindex) {

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/advertise/list') ?>",
            data: {'pageindex':pageindex},
            success: function (page) {

                var strtable = '<table class="table table-striped table-bordered bootstrap-datatable datatable responsive">';
                strtable += '<thead><tr> <th>标题</th> <th>外部链接</th> <th>描述</th> <th>状态</th> <th>操作</th> </tr> </thead>';

                for (var i = 0; i < page.items.length; i++) {
                    var id = page.items[i].id;

                    strtable += "<tr>";
                    strtable += "<td>" + page.items[i].title + "</td>";
                    strtable += "<td>" + page.items[i].link + "</td>";
                    strtable += "<td>" + page.items[i].description + "</td>";

                    strtable += '<td class="center">';
                    if(page.items[i].status == '0') {
                        strtable += '<span class="label-success label label-default">Active</span>';
                    }else{
                        strtable += '<span class="label label-default">Inactive</span>';
                    }
                    strtable += '</td>';

                    strtable += '<td class="center">';
                    strtable += '<a class="btn btn-success" href="#" onclick="showInfoModal()">';
                    strtable += '<i class="glyphicon glyphicon-zoom-in icon-white"></i>';
                    strtable += '查看';
                    strtable += '</a>';
                    strtable += '<a class="btn btn-info" href="#">';
                    strtable += '<i class="glyphicon glyphicon-edit icon-white"></i>';
                    strtable += '编辑';
                    strtable += '</a>';
                    strtable += '<div class="btn-group">';
                    strtable += '<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">';
                    strtable += '<i class="glyphicon glyphicon-edit"></i><span class="hidden-sm hidden-xs"> 状态</span>';
                    strtable += '<span class="caret"></span>';
                    strtable += '</button>';
                    strtable += '<ul class="dropdown-menu">';
                    strtable += '<li class="divider"></li>';
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

                $("#adv-table").html(strtable);
            }
        });
    }

    function changeStatus(id, status, pageindex){
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/advertise/changestatus') ?>",
            data: "id="+id+"&status="+status+"&pageindex="+pageindex,
            success: function () {
                load(pageindex);
            }
        });

    }

    function showInfoModal(id){
        $('#myModal').modal('show').on('shown',function(){
            $.ajax({
                type: "post",
                dataType:"json",
                url: "<?php echo $this->url->get('admin/advertise/detail?id=11') ?>",
                data: {'id' : id},
                success: function (advertise) {
                    $("#title-modal").attr("value", advertise.title);//填充内容
                    $("#link-modal").attr("value", advertise.link);//填充内容
                    $("#description-modal").attr("value", advertise.description);//填充内容
                    $("#memo-modal").attr("value", advertise.memo);//填充内容
                }
            });
        })

    }

    load(pageindexinit);
</script>