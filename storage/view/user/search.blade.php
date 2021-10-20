@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item active">管理员</li>
@endsection
@section('content')
    <script src="/storage/js/validate/user.validate.js"></script>
    <script src="/storage/js/form/user.form.js"></script>

    <div class="card">
        <div class="card-header">
            <form id="user-search" onsubmit="return false;">
                <div class="row">

                    <div class="input-group-append mr-1">
                        <a href="/view/user/create"><button type="button" class="btn btn-block btn-outline-primary"><i class="fas fa-plus"></i></button></a>
                    </div>
                    <div class="col-3">
                        <input type="text" name="name" class="form-control" placeholder="姓名">
                    </div>
                    <div class="col-3">
                        <input type="text" name="email" class="form-control" placeholder="邮箱">
                    </div>
                    <div class="col-3">
                        <input type="text" name="mobile" class="form-control" placeholder="手机号">
                    </div>
                    <div class="input-group-append ml-1">
                        <button type="submit" onclick="handleSearch();" class="btn btn-block btn-outline-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                <tr>
                    <th style="width: 50px">ID</th>
                    <th style="width: 50px">排序</th>
                    <th style="width: 150px">姓名</th>
                    <th style="width: 150px">邮箱</th>
                    <th style="width: 150px">手机号</th>
                    <th style="width: 150px">职位</th>
                    <th style="width: 150px">角色</th>
                    <th style="width: 150px">创建时间</th>
                    <th style="width: 150px">更新时间</th>
                    <th style="width: 150px">操作</th>
                </tr>
                </thead>
                <tbody id="user-list">
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
        // 首次加载页面渲染列表
        renderUserList();

        /**
         * 渲染列表
         *
         * @param param
         */
        function renderUserList(param = {})
        {
            var data = searchUser(param);
            // renderList('user-list', data, ['id', 'name', 'email', 'mobile', 'ctime', 'mtime', 'position'], {'update' : true, 'delete' : true, 'update_url' : '/view/user/update'})
            if (data !== false) {
                $('#user-list').html('');
                var listHtml = '';
                var list = data.list;
                for (var i = 0; i < list.length; i++) {
                    listHtml += '<tr>';
                    listHtml += '<td>' + list[i].id + '</td>';
                    listHtml += '<td>' + list[i].sort + '</td>';
                    listHtml += '<td>' + list[i].name + '</td>';
                    listHtml += '<td>' + list[i].email + '</td>';
                    listHtml += '<td>' + list[i].mobile + '</td>';
                    listHtml += '<td>' + list[i].position + '</td>';
                    listHtml += '<td>';
                    for (var j = 0; j < list[i].role_list.length; j++) {
                        listHtml += '<span class="label label-primary ml-1">' + list[i].role_list[j].name + '</span>';
                    }
                    listHtml += '</td>';
                    listHtml += '<td>' + list[i].ctime + '</td>';
                    listHtml += '<td>' + list[i].mtime + '</td>';
                    listHtml += '<td>';
                    listHtml += '<a href="/view/user/update?id=' + list[i].id + '"><i class="fas fa-edit"></i></a>';
                    if (list[i].root != 1) {
                        listHtml += '<a href="javascript:;" class="ml-2" onclick="handleDelete(' + list[i].id + ')"><i class="fas fa-trash"></i></a>';
                    }
                    listHtml += '</td>';
                    listHtml += '</tr>';
                }
                $('#user-list').html(listHtml);
            }
            renderPage(data);
        }

        /**
         * 搜索处理
         *
         * @param p 页码
         */
        function handleSearch(p = 1)
        {
            handleSearchCallback(function () {
                var searchParam = assembleUserSearchParam(p);
                renderUserList(searchParam);
            });
        }

        function handleDelete(id)
        {
            handleDeleteCallback(function () {
                var param   = {id : id, status : -1}
                var data    = updateUserField(param)
                if (data !== false) {
                    renderUserList();
                }
            });
        }
    </script>
@endsection

