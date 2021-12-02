<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keyword" content="">
    <meta name="description" content="">

    <title></title>

    @include('frontend.layouts.css')
    @include('frontend.layouts.js')

</head>
<body style="min-height:100%;margin:0;padding:0;position:relative;">
<div id="app">
    <!-- 顶部导航 begin -->
    @include('frontend.layouts.top')
    <!-- 顶部导航 end -->
    <main>
    @yield('content')
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
</script>
</body>
</html>
