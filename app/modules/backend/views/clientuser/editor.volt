
<!--/span-->
<!-- left menu ends -->

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
                <a href="#">主页</a>
            </li>
            <li>
                <a href="#">用户管理</a>
            </li>
            <li>
                <a href="#">用户编辑</a>
            </li>
        </ul>
    </div>


    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner profilepage-box">
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-th"></i> {{ username }} 个人信息</h2>

                    <div class="box-icon">
                        <a href="#" class="btn btn-setting btn-round btn-default"><i
                                    class="glyphicon glyphicon-cog"></i></a>
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                    class="glyphicon glyphicon-chevron-up"></i></a>
                        <a href="#" class="btn btn-close btn-round btn-default"><i
                                    class="glyphicon glyphicon-remove"></i></a>
                    </div>
                </div>
                {#<div class="box-content">#}
                    {#<ul class="dashboard-list">#}
                        {#<li>#}
                            {#<strong>账号:</strong> <small>{{ username }}</small> <br>#}
                        {#</li>#}
                        {#<li>#}
                            {#<strong>姓名:</strong> <small>{{ realname }}</small> <br>#}
                        {#</li>#}
                        {#<li>#}
                            {#<strong>电话:</strong> <small>{{ mobile }}</small> <br>#}
                        {#</li>#}
                        {#<li>#}
                            {#<strong>邮箱:</strong> <small>{{ email }}</small> <br>#}
                        {#</li>#}
                        {#<li>#}
                            {#<strong>角色:</strong> <small>{{ profile_name }}</small> <br>#}
                        {#</li>#}
                        {#<li>#}
                            {#<strong>上次登陆时间:</strong> <small> {{ update_time }} </small> </h3><br>#}
                        {#</li>#}
                        {#<li>#}
                            {#<strong>状态:</strong> </h3><span class="label-success label label-default">Approved</span>#}
                        {#</li>#}
                    {#</ul>#}
                {#</div>#}


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

        <!--/span-->

<!-- content ends -->
</div><!--/#content.col-md-0-->

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
