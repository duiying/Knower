@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item"><a href="/admin">首页</a></li>
    <li class="breadcrumb-item active">行为日志</li>
@endsection
@section('content')
    <script src="/storage/js/form/action_log.form.js"></script>

    <div class="card">
        <div class="card-header">
            <form id="tag-search" onsubmit="return false;">
                <div class="row">
                    <div class="col-2">
                        <input type="text" name="account_id" class="form-control" placeholder="用户 ID">
                    </div>
                    <div class="input-group-append ml-1">
                        <button type="submit" onclick="handleSearch();" class="btn btn-block btn-outline-primary"><i class="fa fa-search"></i></button>
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
                    <th style="width: 200px">用户</th>
                    <th style="width: 200px">行为</th>
                    <th style="width: 100px">关联 ID</th>
                    <th style="width: 300px">关联内容</th>
                    <th style="width: 150px">时间</th>
                </tr>
                </thead>
                <tbody id="action-log-list">
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
        renderActionLogList();

        /**
         * 渲染列表
         *
         * @param param
         */
        function renderActionLogList(param = {})
        {
            var data = searchActionLog(param);
            if (data !== false) {
                $('#action-log-list').html('');
                var listHtml = '';
                var list = data.list;
                for (var i = 0; i < list.length; i++) {
                    listHtml += '<tr>';
                    listHtml += '<td>' + list[i].id + '</td>';
                    if (Object.keys(list[i].account_info).length > 0) {
                        listHtml += '<td><div>';
                        listHtml += '<div class="float-left"><img style="height:35px;width:35px;border-radius: 50%;" src="' + list[i].account_info.avatar + '"></div>';
                        listHtml += '<div class="float-left" style="margin-left: 10px;">ID：' + '<span style="color:#6697cb;">' + list[i].account_info.id + '</span>';
                        listHtml += '<br>昵称：';
                        listHtml += '<span style="color:#007bff;">' + list[i].account_info.nickname + '</span>';
                        listHtml += '</div>';
                        listHtml += '</div></td>';
                    } else {
                        listHtml += '<td>游客</td>'
                    }

                    listHtml += '<td>' + list[i].type_text + '</td>';
                    listHtml += '<td>' + list[i].third_id + '</td>';
                    listHtml += '<td>' + list[i].snapshot + '</td>';
                    listHtml += '<td>' + list[i].ctime + '</td>';
                    listHtml += '</tr>';
                }
                $('#action-log-list').html(listHtml);
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
                var searchParam = assembleActionLogSearchParam(p);
                renderActionLogList(searchParam);
            });
        }
    </script>
@endsection