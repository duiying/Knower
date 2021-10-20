<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3</title>

    @include('layouts.css')
    @include('layouts.js')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- 顶部导航 begin -->
    @include('layouts.top')
    <!-- 顶部导航 end -->

    <!-- 左侧导航 begin -->
    @include('layouts.left')
    <!-- 左侧导航 end -->

    <!-- 内容区 begin -->
    <div class="content-wrapper" id="pjax-container">
        <!--  for pjax do not delete this line begin !!!  -->
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div>
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">首页</a></li>
                            @yield('header')
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- /.modal -->
        @yield('content')
        <!--  for pjax do not delete this line end !!!  -->
    </div>
    <!-- 内容区 end -->

    <!-- 底部导航 begin -->
    @include('layouts.footer')
    <!-- 底部导航 end -->

    <div class="modal fade show" id="modal-template" style="display: none;" aria-modal="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="text-center"><i class="fas fa-exclamation-circle" style="color:#f8bb86;font-size: 2rem;"></i></p>
                    <p class="text-center" style="font-size: 1rem;">确定要删除吗？</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick='closeModal()'>取消</button>
                    <button id="doConfirmDelete" type="button" class="btn btn-primary btn-sm">确认</button>
                </div>
            </div>
            <!-- /.modal-content -->n
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
<!-- ./wrapper -->
</body>

<!-- pjax -->
<script>
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

</html>