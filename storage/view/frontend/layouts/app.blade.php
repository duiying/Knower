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
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8" style="padding-right: 0;">
                    <div class="card" style="margin-top:20px;">
                        <div class="card-body">
                                <h5 style="padding-top:{{ $key>0? '10px':'0'}};padding-bottom:5px;"><strong>
                                        <a href="/">111</a>
                                    </strong>
                                </h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div style="margin-bottom: 5px;">
                                            <a href="">2222</a>
                                        </div>
                                        <div>
                                            <i class="fa fa-clock-o"></i>&nbsp;1
                                            &nbsp;&nbsp;
                                            <i class="fa fa-eye"></i>&nbsp;2
                                            &nbsp;&nbsp;
                                            <i class="fa fa-comments-o"></i>&nbsp;3
                                        </div>
                                    </div>
                                        <div class="col-md-4">
                                            <a href=""></a>
                                        </div>
                                </div>
                                <hr>
                            <div></div>
                        </div>
                    </div>

                </div>
                <div class="col-md-4" style="padding-left:20px;">
                    <div class="card" style="margin-top:20px;">
                        <div class="card-body">
                            <h5>关注</h5>
                            <hr>

                        </div>
                    </div>

                    <div style="margin-top:20px;">
                        <!-- 标签 begin -->
                        @include('frontend.layouts.tag')
                        <!-- 标签 end -->
                    </div>

                </div>
            </div>
        </div>
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
