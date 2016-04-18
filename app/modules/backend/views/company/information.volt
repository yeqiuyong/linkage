
<div class="box-content">
    <ul class="dashboard-list" id="company-info">
    </ul>
</div>
</div>
</div>


<script type="text/javascript">
    function showCompanyInfo(id){
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/company/information') ?>",
            data: {'id' : id},
            success: function (information) {
                $("#company-info").append("<li><strong>公司名称:</strong> <small>"+ information.name +"</small><br> </li>");
                $("#company-info").append("<li><strong>类型:</strong> <small>"+ information.type +"</small><br> </li>");
                $("#company-info").append("<li><strong>联系人:</strong> <small>"+ information.contactor +"</small><br> </li>");
                $("#company-info").append("<li><strong>联系地址:</strong> <small>"+ information.address +"</small><br> </li>");
                $("#company-info").append("<li><strong>联系邮件:</strong> <small>"+ information.email +"</small><br> </li>");
                $("#company-info").append("<li><strong>服务电话1:</strong> <small>"+ information.service_phone1 +"</small><br> </li>");
                $("#company-info").append("<li><strong>服务电话2:</strong> <small>"+ information.service_phone2 +"</small><br> </li>");
                $("#company-info").append("<li><strong>服务电话3:</strong> <small>"+ information.service_phone3 +"</small><br> </li>");
                $("#company-info").append("<li><strong>服务电话4:</strong> <small>"+ information.service_phone4 +"</small><br> </li>");
                $("#company-info").append("<li><strong>公司简介:</strong> <small>"+ information.description +"</small><br> </li>");
                $("#company-info").append("<li><strong>注册时间:</strong> <small>"+ information.create_time +"</small><br> </li>");
                $("#company-info").append('<li><strong>状态:</h3><span class="label-success label label-default">Approved</span>');
            }
        });

    }
</script>