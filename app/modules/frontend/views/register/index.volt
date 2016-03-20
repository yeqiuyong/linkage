
{{ content() }}

<div class="page-header">
    <h2>Register for Linkage</h2>
</div>

<form id="registerForm" action="" method="post" >

    <fieldset>
        <input type="text" name="cn" id="cn" value="{{ cn }}" hidden="true">

        <div class="control-group">
            <label class="control-label" for="selectError">注册类型</label>

            <div class="controls">
                <select id="usertype" name="usertype" data-rel="chosen">
                    <option value ="0">厂商</option>
                    <option value ="1">承运商</option>
                    <option value ="2">司机</option>
                </select>
            </div>
        </div>


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

{{ javascript_include('js/jquery.md5.js') }}

<script type="text/javascript">
    $("#get-vcode-btn").click(function(){
        var mobile = $("input[id='mobile']").val();

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('register/verifycode') ?>",
            data: "mobile=" + mobile,
            success:function(data){
                if(data.code == 0){
                    var count = 60;
                    var myCountdown = setInterval(countDown, 1000);

                    function countDown() {
                        $("#get-vcode-btn").attr("disabled", true);
                        $("#get-vcode-btn").val(count + " s");
                        if (count == 0) {
                            clearInterval(myCountdown);
                        }
                        count--;
                    }

                }else{
                    alert(data.message);
                }
            }
        });

    });

    $("#register-form-btn").click(function(){
        var cn = $("input[id='cn']").val();
        var mobile = $("input[id='mobile']").val();
        var password = $("input[id='password']").val();
        var repeatPassword = $("input[id='repeatPassword']").val();
        var verifyCode = $("input[id='verifyCode']").val();

        if(cn == null || cn == ''){
            alert("邀请链接错误！");
            return;
        }

        if(password == null || password == ''){
            alert("请输入密码");
            return;
        }

        if(repeatPassword == null || repeatPassword == ''){
            alert("请确认密码");
            return;
        }

        if(verifyCode == null || verifyCode == ''){
            alert("请输入校验码");
            return;
        }

        if(password != repeatPassword){
            alert("校验密码不一致");
            return;
        }

        var pwd = $.md5(password);
        var userType = $("#usertype").val();

        $.ajax({
            type: "post",
            dataType:"json",
            url: "<?php echo $this->url->get('register/register') ?>",
            data: "cn="+cn+"&mobile="+mobile+"&password="+pwd+"&ctype="+userType+"&verify_code="+verifyCode,
            success:function(data){
                if(data.code == 0){
                    window.location.href=data.url;
                }else{
                    alert(data.message);
                }
            },

            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(XMLHttpRequest.status);
                alert(XMLHttpRequest.readyState);
                alert(textStatus);
            }
        });
    });

</script>