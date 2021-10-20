@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item"><a href="/view/role/search">角色</a></li>
    <li class="breadcrumb-item active">创建</li>
@endsection
@section('content')
    <script src="/storage/js/validate/role.validate.js"></script>
    <script src="/storage/js/form/role.form.js"></script>

    <div class="row">
        <div class="col-12">
            <!-- /.card -->
            <!-- Horizontal Form -->
            <div class="card card-info">
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" id="role-create" onsubmit="return false;">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">名称<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" placeholder="名称">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">菜单</label>
                            <div class="col-sm-10">
                                <div class="form-group" id="role-menu-check">

                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">权限</label>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <select class="duallistbox" multiple="multiple" id="rolePermissionSelect">
                                    </select>
                                </div>
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
            if (validateRoleParam('role-create')) {
                var param   = assembleRoleFormParam()
                var data    = createRole(param)
                if (data !== false) {
                    pjaxToUrl('/view/role/search');
                }
            }
        }

        renderPermissionSelect(selectPermission())

        renderMenuSelect(selectMenu());
    </script>
@endsection

