<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Knower 知者开源项目</title>
    <meta name="keyword" content="Knower,PHP,Hyperf,对影,duiying,博客,内容管理,开源,Redis,前后端分离,RBAC,ElasticSearch,权限管理">
    <meta name="description" content="Knower 知者开源项目">
    <link rel="shortcut icon" href="/storage/img/shortcut.png" />
    <title></title>

    @include('frontend.layouts.css')
    @include('frontend.layouts.js')

</head>
<body style="min-height:100%;margin:0;padding:0;position:relative;">
<div id="app">
    <!-- 顶部导航 begin -->
    @include('frontend.layouts.top')
    <!-- 顶部导航 end -->
    <main id="pjax-container">
    <!--  for pjax do not delete this line begin !!!  -->
    @yield('content')
    <!--  for pjax do not delete this line end !!!  -->
    </main>
    <!-- 底部导航 begin -->
    @include('frontend.layouts.footer')
    <!-- 底部导航 end -->
</div>
<script>
    if ($(window).height() - $('#app').height() > 30) {
        $('#footer').css('position', 'fixed');
        $('#footer').css('bottom', 0);
        $('#footer').css('width', '100%');
    }

    <!-- pjax -->
    $.pjax.defaults.timeout = 5;
    $(document).pjax('a:not(a[target="_blank"])', {
        container: '#pjax-container'
    });
    $(document).on('pjax:start', function() {
        NProgress.start();
    });
    $(document).on('pjax:end', function() {
        NProgress.done();
    });
    $(document).on("pjax:timeout", function(event) {
        event.preventDefault()
    });
    $(document).on('submit', 'form[pjax-container]', function (event) {
        $.pjax.submit(event, '#pjax-container');
    });
</script>
</body>
</html>
