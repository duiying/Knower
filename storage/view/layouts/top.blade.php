<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fa fa-user-circle"></i> <span class="user-name"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="/storage/AdminLTE/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title mt-1">
                                <span class="user-name"></span>
                            </h3>
                            <p class="user-position" style="font-size: 0.5rem;color: gray;"></p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-default">设置</button>
                    <button type="submit" class="btn btn-default float-right" onclick="logout()">退出</button>
                </div>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<script>
    var data = getUserInfo();
    if (data !== false) {
        $('.user-name').html(data.name);
        $('.user-position').html(data.position);
    }
    
    function logout()
    {
        NProgress.start();
        var data = userLogout();
        if (data !== false) {
            NProgress.done();
            // 500 毫秒后跳转到登录页
            setTimeout(function () {
                location.href = '/view/user/login';
            }, 500)
        }
        NProgress.done();
    }
</script>