<div class="ch-container">
    <div class="row">

        <div class="row">
            <div class="col-md-12 center login-header">
                <h2>有米管理后台</h2>
            </div>
            <!--/span-->
        </div><!--/row-->

        <div class="row">
            <div class="well col-md-5 center login-box">
                <div class="alert alert-info">
                   请输入你的账户名和密码
                </div>
                <form id="form" action="" method="post" >
                    <fieldset>
                        <div class="input-group input-group-lg">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user red"></i></span>
                            {{ text_field('username', 'class': "form-control") }}
                        </div>
                        <div class="clearfix"></div><br>

                        <div class="input-group input-group-lg">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock red"></i></span>
                            {{ password_field('password', 'class': "form-control") }}
                        </div>
                        <div class="clearfix"></div>

                        <div class="input-prepend">
                            <label class="remember" for="remember"><input type="checkbox" id="remember"> Remember me</label>
                        </div>
                        <div class="clearfix"></div>

                        <p class="center col-md-5">
                            <input name="" type="button" id="login-form" class="btn btn-primary" value="登陆" >
                        </p>
                    </fieldset>
                </form>
            </div>
            <!--/span-->
        </div><!--/row-->
    </div><!--/fluid-row-->
</div><!--/.fluid-container-->

<script type="text/javascript">
    $("#login-form").click(function(){
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('admin/session/login') ?>",
            data:$("form").serialize(),
            success:function(data){
                if(data.status==0){
                    window.location.href=data.url;
                }else{
                    alert(data.msg);
                }
            }
        });

    });
</script>