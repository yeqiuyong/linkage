
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
                <a href="#">公司详情</a>
            </li>
        </ul>
    </div>


    <div class="box-content">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#information" onclick="showCompanyInfo({{ company_id }})">资料</a></li>
            <li><a href="#profile" onclick="initProfileNav({{ company_id }})">概况</a></li>
            <li class="dropdown">
                <a href="#" id="myTabDrop" class="dropdown-toggle"
                   data-toggle="dropdown">发送信息
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop">
                    <li><a href="#sms" tabindex="-1" data-toggle="tab">sms</a></li>
                    <li><a href="#email" tabindex="-1" data-toggle="tab">邮件</a></li>
                    <li><a href="#message" tabindex="-1" data-toggle="tab">站内信息</a></li>
                    <li><a href="#push" tabindex="-1" data-toggle="tab">推送</a></li>
                </ul>
            </li>
        </ul>

        <div id="myTabContent" class="tab-content">
            <div class="tab-pane active" id="information">
                <div class="box col-md-12" >
                    <div class="box-inner">
                        <div class="box-header well" data-original-title="公司资料">
                            <div class="box-icon">
                                <a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
                                <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                            class="glyphicon glyphicon-chevron-up"></i></a>
                                <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
                            </div>
                        </div>
                        <div id="information-table" class="box-content">
                        </div>

                        {% include "company/information.volt" %}
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="profile">
                <div class="box col-md-12" >
                    {% include "company/profile.volt" %}
                </div>
            </div>

            <div class="tab-pane" id="sms">
                <div class="box col-md-12" >
                    <div class="box-inner">
                        <div class="box-header well" data-original-title="消息推送">
                            <div class="box-icon">
                                <a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
                                <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                            class="glyphicon glyphicon-chevron-up"></i></a>
                                <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
                            </div>
                        </div>
                        <div id="message-table" class="box-content">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>


<script type="text/javascript">
    showCompanyInfo({{ company_id }});
</script>
<!-- Ad ends -->
