@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item"><a href="/admin">首页</a></li>
    <li class="breadcrumb-item active">用户</li>
@endsection
@section('content')
    <script src="/storage/js/form/account.form.js"></script>

    <div class="card">
        <div class="card-header">
            <form id="tag-search" onsubmit="return false;">
                <div class="row">
                    <div class="col-2">
                        <input type="text" name="id" class="form-control" placeholder="用户 ID">
                    </div>
                    <div class="col-2">
                        <input type="text" name="nickname" class="form-control" placeholder="昵称">
                    </div>
                    <div class="col-2">
                        <input type="text" name="email" class="form-control" placeholder="邮箱">
                    </div>
                    <div class="col-2">
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
                    <th style="width: 150px">头像</th>
                    <th style="width: 150px">昵称</th>
                    <th style="width: 150px">邮箱</th>
                    <th style="width: 150px">手机号</th>
                    <th style="width: 150px">状态</th>
                    <th style="width: 150px">最近活跃时间</th>
                    <th style="width: 150px">注册时间</th>
                    <th style="width: 150px">操作</th>
                </tr>
                </thead>
                <tbody id="account-list">
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
        renderAccountList();

        /**
         * 渲染列表
         *
         * @param param
         */
        function renderAccountList(param = {})
        {
            var data = searchAccount(param);
            if (data !== false) {
                $('#account-list').html('');
                var listHtml = '';
                var list = data.list;
                for (var i = 0; i < list.length; i++) {
                    listHtml += '<tr>';
                    listHtml += '<td>' + list[i].id + '</td>';
                    listHtml += '<td><img style="height:35px;width:35px;border-radius: 50%;" src="' + list[i].avatar + '"></td>';
                    listHtml += '<td>' + list[i].nickname + '</td>';
                    listHtml += '<td>' + list[i].email + '</td>';
                    listHtml += '<td>' + list[i].mobile + '</td>';
                    listHtml += '<td>';
                    if (list[i].status === 1) {
                        listHtml += '<span class="label label-success">' + list[i].status_text + '</span>';
                    } else {
                        listHtml += '<span class="label label-warning">' + list[i].status_text + '</span>';
                    }
                    listHtml += '</td>';
                    listHtml += '<td>' + list[i].last_active_time + '</td>';
                    listHtml += '<td>' + list[i].ctime + '</td>';
                    listHtml += '<td>';
                    if (list[i].status === 1) {
                        listHtml += '<a href="javascript:;" class="ml-2" style="color:#dc3545;" onclick="handleIllegal(' + list[i].id + ')">封禁</a>';
                    } else {
                        listHtml += '<a href="javascript:;" class="ml-2" style="color:#5cb85c;" onclick="handleNormal(' + list[i].id + ')">解封</a>';
                    }
                    listHtml += '</td>';
                    listHtml += '</tr>';
                }
                $('#account-list').html(listHtml);
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
                var searchParam = assembleAccountSearchParam(p);
                renderAccountList(searchParam);
            });
        }

        function handleIllegal(id)
        {
            var param   = {id : id, status : -1}
            var data    = updateAccountField(param)
            if (data !== false) {
                renderAccountList();
            }
        }

        function handleNormal(id)
        {
            var param   = {id : id, status : 1}
            var data    = updateAccountField(param)
            if (data !== false) {
                renderAccountList();
            }
        }
    </script>
@endsection

