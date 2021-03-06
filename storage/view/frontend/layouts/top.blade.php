<nav class="navbar navbar-expand-md navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="/">
            Knower
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li><a class="nav-link active" href="/">首页</a></li>
                <!-- <li><a class="nav-link" href="">专栏</a></li> -->
            </ul>
            <ul class="navbar-nav ml-auto" id="account-login">
                <!-- Authentication Links -->
                <li><a class="nav-link" href="/login">登录</a></li>
            </ul>
            <ul class="navbar-nav ml-auto" id="account-logout" style="display: none;">
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img style="height:25px;border-radius: 50%;" src="" id="user-avatar"> <span id="user-nickname">duiying123</span> <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" id="logout" href="javascript:;">
                            退出登录
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script type="text/javascript">
    function renderLogin()
    {
        if ($.cookie('knower_access_token')) {
            var data = getInfoByToken();
        } else {
            var data = false;
        }

        // 已登录
        if (data !== false && data.id !== undefined) {
            $('#account-logout').css('display', 'inline-block');
            $('#account-login').css('display', 'none');
            $('#user-nickname').html(data.nickname);
            $('#user-avatar').attr('src', data.avatar);
        }
        // 未登录
        else {
            $('#account-login').css('display', 'inline-block');
            $('#account-logout').css('display', 'none');
        }
    }

    renderLogin();

    // 退出登录
    $('#logout').click(function () {
        $('#account-login').css('display', 'inline-block');
        $('#account-logout').css('display', 'none');
        location.href = '/account/logout';
    });
</script>
