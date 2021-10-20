@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item"><a href="/view/menu/search">菜单</a></li>
    <li class="breadcrumb-item active">更新</li>
@endsection
@section('content')
    <script src="/storage/js/validate/menu.validate.js"></script>
    <script src="/storage/js/form/menu.form.js"></script>

    <input name="id" type="hidden" value="{{ $id }}">

    <div class="row">
        <div class="col-12">
            <!-- /.card -->
            <!-- Horizontal Form -->
            <div class="card card-info">
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" id="menu-update" onsubmit="return false;">
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

        var data = findMenu({id : $('input[name=id]').val()});

        // 渲染表单数据
        renderUpdateForm(data, ['name', 'icon', 'url', 'sort']);
        // 渲染下拉选择
        if (data !== false) {
            $('select[name=pid] > option[value=' + data.pid + ']').attr('selected', true);
            if (data.pid != 0) {
                $('#form-group-url').removeClass('none');
            }
        }

        function handleSubmit()
        {
            if (validateMenuParam('menu-update')) {
                var param   = assembleMenuFormParam(true)
                var data    = updateMenu(param)
                if (data !== false) {
                    pjaxToUrl('/view/menu/search');
                }
            }
        }
    </script>
@endsection

