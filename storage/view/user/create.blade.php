@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item"><a href="/view/user/search">管理员</a></li>
    <li class="breadcrumb-item active">创建</li>
@endsection
@section('content')
    <script src="/storage/js/validate/user.validate.js"></script>
    <script src="/storage/js/form/user.form.js"></script>

    <div class="row">
        <div class="col-12">
            <!-- /.card -->
            <!-- Horizontal Form -->
            <div class="card card-info">
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" id="user-create" onsubmit="return false;">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">姓名<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" placeholder="真实姓名">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">手机<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="mobile" class="form-control" placeholder="手机号">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">邮箱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control" placeholder="公司邮箱">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">职位<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="position" class="form-control" placeholder="职位">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">角色</label>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <select class="duallistbox" multiple="multiple" id="roleSelect">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">密码<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="password" name="password" class="form-control" placeholder="密码">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">确认密码<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="password" name="rePassword" class="form-control" placeholder="确认密码">
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
            if (validateParam('user-create')) {
                var param   = assembleUserFormParam()
                var data    = createUser(param)
                if (data !== false) {
                    pjaxToUrl('/view/user/search');
                }
            }
        }

        renderRoleSelect(selectRole())
    </script>
@endsection

