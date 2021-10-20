<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录</title>

    @include('layouts.js')
    @include('layouts.css')
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="../../index2.html"><b>Admin</b>LTE</a>
    </div>

    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">运营后台</p>

            <form onsubmit="return false;">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="email" placeholder="邮箱">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="密码">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block" onclick="handleLogin();">登录</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <div class="social-auth-links text-center mb-3">
                <p>- OR -</p>
                <a href="#" class="btn btn-block btn-primary">
                    <i class="fab fa-github mr-2"></i> Sign in using GitHub
                </a>
            </div>
            <!-- /.social-auth-links -->
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->
</body>
<script>
    function handleLogin()
    {
        var param = {
            email       :  $('input[name=email]').val(),
            password    :  $('input[name=password]').val()
        }
        if (param.email == '') {
            alert.error('请输入邮箱！');
            return;
        }
        if (param.password == '') {
            alert.error('请输入密码！');
            return;
        }

        var loginData = userLogin(param);
        if (loginData !== false) {
            // 设置 token 两个小时之后过期
            var date = new Date();
            date.setTime(date.getTime() + loginData.expire * 1000);
            $.cookie('access_token', loginData.access_token, { path: '/', expires: date});

            // 500 毫秒后跳转到首页
            setTimeout(function () {
                location.href = '/';
            }, 500)
        }
    }
</script>
</html>
