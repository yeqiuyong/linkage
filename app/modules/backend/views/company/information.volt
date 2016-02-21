
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
                <a href="#">公司管理</a>
            </li>
            <li>
                <a href="#">公司信息</a>
            </li>
        </ul>
    </div>


    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner companypage-box">
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-th"></i> {{ name }} 信息</h2>

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
                    <ul class="dashboard-list">
                        <li>
                            <strong>公司名称:</strong> <small>{{ name }}</small> <br>
                        </li>
                        <li>
                            <strong>类型:</strong> <small>{{ type }}</small> <br>
                        </li>
                        <li>
                            <strong>企业代号:</strong> <small>{{ code }}</small> <br>
                        </li>
                        <li>
                            <strong>联系人:</strong> <small>{{ contactor }}</small> <br>
                        </li>
                        <li>
                            <strong>联系地址:</strong> <small>{{ address }}</small> <br>
                        </li>
                        <li>
                            <strong>联系邮件:</strong> <small> {{ email }} </small> </h3><br>
                        </li>
                        <li>
                            <strong>服务电话1:</strong> <small> {{ service_phone1 }} </small> </h3><br>
                        </li>
                        <li>
                            <strong>服务电话2:</strong> <small> {{ service_phone2 }} </small> </h3><br>
                        </li>
                        <li>
                            <strong>服务电话3:</strong> <small> {{ service_phone3 }} </small> </h3><br>
                        </li>
                        <li>
                            <strong>服务电话4:</strong> <small> {{ service_phone4 }} </small> </h3><br>
                        </li>
                        <li>
                            <strong>企业简介:</strong> <small> {{ description }} </small> </h3><br>
                        </li>
                        <li>
                            <strong>更新时间:</strong> <small> {{ update_time }} </small> </h3><br>
                        </li>
                        <li>
                            <strong>状态:</strong> </h3><span class="label-success label label-default">Approved</span>
                        </li>
                    </ul>
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
