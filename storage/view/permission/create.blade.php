@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item"><a href="/view/permission/search">权限</a></li>
    <li class="breadcrumb-item active">创建</li>
@endsection
@section('content')
    <script src="/storage/js/validate/permission.validate.js"></script>
    <script src="/storage/js/form/permission.form.js"></script>

    <div class="row">
        <div class="col-12">
            <!-- /.card -->
            <!-- Horizontal Form -->
            <div class="card card-info">
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" id="permission-create" onsubmit="return false;">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">名称<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" placeholder="名称">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">路由<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea type="text" name="url" class="form-control" placeholder="路由（多个路由之间请用英文分号 ; 隔开）"></textarea>
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
        function handleSubmit()
        {
            if (validatePermissionParam('permission-create')) {
                var param   = assemblePermissionFormParam()
                var data    = createPermission(param)
                if (data !== false) {
                    pjaxToUrl('/view/permission/search');
                }
            }
        }
    </script>
@endsection

