
{{ content() }}

<div class="page-header">
    <h2>Register for Linkage</h2>
</div>

<form id="registerForm" action="" method="post" >

    <fieldset>
        <div class="control-group">
            <label class="control-label" for="mobile">手机</label>
            <div class="controls">
                {{ text_field('mobile', 'class': "form-control") }}

                <div class="alert alert-warning" id="username_alert">
                    <strong>警告!</strong> 请输入电话号码！
                </div>
            </div>
        </div>


        <div class="control-group">
            <label class="control-label" for="password">密码</label>
            <div class="controls">
                {{ password_field('password', 'class': 'form-control') }}

                <div class="alert alert-warning" id="password_alert">
                    <strong>警告!</strong> 请输入密码！
                </div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="repeatPassword">确认密码</label>
            <div class="controls">
                {{ password_field('repeatPassword', 'class': 'form-control') }}
                <div class="alert" id="repeatPassword_alert">
                    <strong>警告!</strong> 密码不相符
                </div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="verifyCode">校验码</label>
            <div class="controls">
                {{ text_field('verifyCode', 'class': 'form-control') }}
                <div class="alert alert-warning" id="password_alert">
                    <strong>警告!</strong> 请输入密码！
                </div>
            </div>
        </div>

        <div class="form-actions">
            <input name="" type="button" id="get-vcode-btn" class="btn btn-primary" value="获取验证码" >
        </div>

        <div class="form-actions">
            <input name="" type="button" id="register-form-btn" class="btn btn-primary" value="注册" >
        </div>

    </fieldset>
</form>


<script type="text/javascript">
    $("#get-vcode-btn").click(function(){
        var mobile = $("input[id='mobile']").val();

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('register/verifycode') ?>",
            data: "mobile=" + mobile,
            success:function(data){
                if(data.result==0){
                    alert('aaaaa');
                }else{
                    alert(data.reason);
                }
            }
        });

    });

    $("#register-form-btn").click(function(){
        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('register/register') ?>",
            data:$("registerForm").serialize(),
            success:function(data){
                if(data.result==0){
                    window.location.href=data.url;
                }else{
                    alert(data.reason);
                }
            }
        });

    });
</script>