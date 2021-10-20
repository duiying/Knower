@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item active">权限</li>
@endsection
@section('content')
    <style>
        .none {display: none;}
    </style>
    <script src="/storage/js/validate/permission.validate.js"></script>
    <script src="/storage/js/form/permission.form.js"></script>
    <div class="card">
        <div class="card-header">
            <form id="user-search" onsubmit="return false;">
                <div class="row">
                    <div class="input-group-append mr-1">
                        <a href="/view/permission/create"><button type="button" class="btn btn-block btn-outline-primary"><i class="fas fa-plus"></i></button></a>
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
                    <th style="width: 150px">路由</th>
                    <th style="width: 150px">操作</th>
                </tr>
                </thead>
                <tbody id="permission-list">
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
        function renderPermissionList(param = {})
        {
            var data = searchPermission(param);
            if (data !== false) {
                $('#permission-list').html('');
                var listHtml = '';
                var list = data.list;
                for (var i = 0; i < list.length; i++) {
                    listHtml += '<tr>';
                    listHtml += '<td>' + list[i].id + '</td>';
                    listHtml += '<td>' + list[i].sort + '</td>';
                    listHtml += '<td>' + list[i].name + '</td>';
                    listHtml += '<td>';
                    for (var j = 0; j < list[i].url_list.length; j++) {
                        listHtml += '<code>' + list[i].url_list[j] + '</code>'
                        listHtml += '<br>';
                    }

                    listHtml += '</td>';
                    listHtml += '<td>';
                    listHtml += '<a href="/view/permission/update?id=' + list[i].id + '"><i class="fas fa-edit"></i></a>';
                    listHtml += '<a href="javascript:;" class="ml-2" onclick="handlePermissionDelete(' + list[i].id + ')"><i class="fas fa-trash"></i></a>';
                    listHtml += '</td>';
                    listHtml += '</tr>';
                }
                $('#permission-list').html(listHtml);
            }
            renderPage(data);
        }

        renderPermissionList();

        function handleSearch(p = 1)
        {
            handleSearchCallback(function () {
                renderPermissionList({p : p});
            });
        }

        function handlePermissionDelete(id)
        {
            handleDeleteCallback(function () {
                var param   = {id : id, status : -1}
                var data    = updatePermissionField(param)
                if (data !== false) {
                    renderPermissionList();
                }
            });
        }
    </script>
@endsection

