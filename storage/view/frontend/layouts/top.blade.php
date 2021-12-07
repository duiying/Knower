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
                <li><a class="nav-link active" href="">首页</a></li>
                <li><a class="nav-link" href="">专栏</a></li>
                <li><a class="nav-link" href="">教程</a></li>
            </ul>

            <form action="/" class="form-inline my-2 my-lg-0" method="get">
                <input class="form-control mr-sm-2" type="text" name="q" placeholder="搜索" value="">
                <button class="btn btn-outline-secondary my-2 my-sm-0" type="submit">搜索</button>
            </form>

            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                <li><a class="nav-link" href="/oauth/github">登录</a></li>
                <li><a class="nav-link" href="">注册</a></li>
            </ul>
        </div>
    </div>
</nav>
