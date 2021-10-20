@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item active">角色</li>
@endsection
@section('content')
    <style>
        .none {display: none;}
    </style>
    <script src="/storage/js/validate/role.validate.js"></script>
    <script src="/storage/js/form/role.form.js"></script>
    <div class="card">
        <div class="card-header">
            <form id="user-search" onsubmit="return false;">
                <div class="row">
                    <div class="input-group-append mr-1">
                        <a href="/view/role/create"><button type="button" class="btn btn-block btn-outline-primary"><i class="fas fa-plus"></i></button></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                <tr>
                    <th style="width: 50px">ID</th>
                    <th style="width: 50px">排序</th>
                    <th style="width: 50px">名称</th>
                    <th style="width: 50px">超级管理员</th>
                    <th style="width: 150px">权限</th>
                    <th style="width: 150px">菜单</th>
                    <th style="width: 150px">操作</th>
                </tr>
                </thead>
                <tbody id="role-list">
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->

        <div class="card-footer clearfix" id="pagination">
            <div class="float-left" id="pagination-total"></div>
            <ul class="pagination pagination-sm m-0 float-right">

            </ul>
        </div>
    </div>
    <!-- /.card -->
    <script type="text/javascript">
        function renderRoleList()
        {
            var data = searchRole();
            if (data !== false) {
                $('#role-list').html('');
                var listHtml = '';
                var list = data.list;
                for (var i = 0; i < list.length; i++) {
                    listHtml += '<tr>';
                    listHtml += '<td>' + list[i].id + '</td>';
                    listHtml += '<td>' + list[i].sort + '</td>';
                    listHtml += '<td>' + list[i].name + '</td>';
                    listHtml += '<td>';
                    if (list[i].admin == 1) listHtml += '<span class="label label-success">是</span>';
                    listHtml += '</td>';
                    listHtml += '<td>';
                    if (list[i].admin != 1) {
                        for (var j = 0; j < list[i].permission_list.length; j++) {
                            listHtml += '<span class="label label-primary ml-1">' + list[i].permission_list[j].name + '</span>';
                        }
                    } else {
                        listHtml += '<span class="label label-primary">所有权限</span>';
                    }
                    listHtml += '</td>';
                    listHtml += '<td>';
                    if (list[i].admin != 1) {
                        for (var j = 0; j < list[i].menu_list.length; j++) {
                            listHtml += '<span class="label label-primary ml-1">' + list[i].menu_list[j].name + '</span>';
                        }
                    } else {
                        listHtml += '<span class="label label-primary">所有菜单</span>';
                    }
                    listHtml += '</td>';
                    listHtml += '<td>';
                    if (list[i].admin != 1) {
                        listHtml += '<a href="/view/role/update?id=' + list[i].id + '"><i class="fas fa-edit"></i></a>';
                        listHtml += '<a href="javascript:;" class="ml-2" onclick="handleRoleDelete(' + list[i].id + ')"><i class="fas fa-trash"></i></a>';
                    }
                    listHtml += '</td>';
                    listHtml += '</tr>';
                }
                $('#role-list').html(listHtml);
            }
            renderPage(data);
        }

        renderRoleList();

        function handleRoleDelete(id)
        {
            handleDeleteCallback(function () {
                var param   = {id : id, status : -1}
                var data    = updateRoleField(param)
                if (data !== false) {
                    renderRoleList();
                }
            });
        }
    </script>
@endsection

