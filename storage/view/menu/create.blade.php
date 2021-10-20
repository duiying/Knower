@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item"><a href="/view/menu/search">菜单</a></li>
    <li class="breadcrumb-item active">创建</li>
@endsection
@section('content')
    <script src="/storage/js/validate/menu.validate.js"></script>
    <script src="/storage/js/form/menu.form.js"></script>

    <div class="row">
        <div class="col-12">
            <!-- /.card -->
            <!-- Horizontal Form -->
            <div class="card card-info">
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" id="menu-create" onsubmit="return false;">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">上级<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-control select2" id="pid-select" name="pid">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">标题<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" placeholder="标题">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">图标<span class="text-danger">*</span>（<a href="http://www.fontawesome.com.cn/faicons/" target="_blank">fontawesome</a>）</label>
                            <div class="col-sm-10">
                                <input type="text" name="icon" class="form-control" placeholder="图标，例如：fa fa-bars">
                            </div>
                        </div>
                        <div class="form-group row" id="form-group-url">
                            <label class="col-sm-2 col-form-label">路由<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="url" class="form-control" placeholder="路由">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">排序（升序）</label>
                            <div class="col-sm-10">
                                <input type="text" name="sort" class="form-control" placeholder="排序" value="99">
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right" onclick="handleSubmit();">提交</button>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>

    <script type="text/javascript">
        // 渲染下拉选择
        renderMenuPidSelect()

        function handleSubmit()
        {
            if (validateMenuParam('menu-create')) {
                var param   = assembleMenuFormParam()
                var data    = createMenu(param)
                if (data !== false) {
                    pjaxToUrl('/view/menu/search');
                }
            }
        }
    </script>
@endsection

