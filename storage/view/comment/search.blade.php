@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item active">评论</li>
@endsection
@section('content')
    <script src="/storage/js/form/comment.form.js"></script>

    <div class="card">
        <div class="card-header">
            <form id="tag-search" onsubmit="return false;">
                <div class="row">
                    <div class="col-2">
                        <select class="form-control select2" id="audit-select" name="audit">
                            <option value="">审核状态：全部</option>
                            <option value="0">待审核</option>
                            <option value="1">已审核</option>
                            <option value="-1">已违规</option>
                        </select>
                        <script>
                            $('#audit-select').select2();
                        </script>
                    </div>
                    <div class="col-2">
                        <select class="form-control select2" id="status-select" name="status">
                            <option value="">评论状态：全部</option>
                            <option value="1">正常</option>
                            <option value="-1">已删除</option>
                        </select>
                        <script>
                            $('#status-select').select2();
                        </script>
                    </div>
                    <div class="col-2">
                        <input type="text" name="account_id" class="form-control" placeholder="用户 ID">
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
                    <th style="width: 200px">用户</th>
                    <th style="width: 200px">内容</th>
                    <th style="width: 100px">审核状态</th>
                    <th style="width: 100px">状态</th>
                    <th style="width: 150px">评论时间</th>
                    <th style="width: 150px">操作</th>
                </tr>
                </thead>
                <tbody id="comment-list">
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
        renderCommentList();

        /**
         * 渲染列表
         *
         * @param param
         */
        function renderCommentList(param = {})
        {
            var data = searchComment(param);
            if (data !== false) {
                $('#comment-list').html('');
                var listHtml = '';
                var list = data.list;
                for (var i = 0; i < list.length; i++) {
                    listHtml += '<tr>';
                    listHtml += '<td>' + list[i].id + '</td>';
                    listHtml += '<td><div>';
                    listHtml += '<div class="float-left"><img style="height:35px;width:35px;border-radius: 50%;" src="' + list[i].account_info.avatar + '"></div>';
                    listHtml += '<div class="float-left" style="margin-left: 10px;">ID：' + '<span style="color:#6697cb;">' + list[i].account_info.id + '</span>';
                    listHtml += '<br>昵称：';
                    listHtml += '<span style="color:#007bff;">' + list[i].account_info.nickname + '</span>';
                    listHtml += '</div>';
                    listHtml += '</div></td>';
                    listHtml += '<td>' + list[i].content + '</td>';
                    listHtml += '<td>';
                    if (list[i].audit === 0) {
                        listHtml += '<span class="label label-primary">' + list[i].audit_text + '</span>';
                    } else if (list[i].audit === 1) {
                        listHtml += '<span class="label label-success">' + list[i].audit_text + '</span>';
                    } else {
                        listHtml += '<span class="label label-warning">' + list[i].audit_text + '</span>';
                    }
                    listHtml += '</td>';
                    listHtml += '<td>';
                    if (list[i].status === 1) {
                        listHtml += '<span class="label label-success">' + list[i].status_text + '</span>';
                    } else {
                        listHtml += '<span class="label label-warning">' + list[i].status_text + '</span>';
                    }
                    listHtml += '</td>';
                    listHtml += '<td>' + list[i].ctime + '</td>';
                    listHtml += '<td>';
                    if (list[i].status === 1) {
                        listHtml += '<a href="javascript:;" class="ml-2" onclick="handleDelete(' + list[i].id + ')"><i class="fas fa-trash"></i></a>';
                    }
                    if (list[i].audit === 0) {
                        listHtml += '<a href="javascript:;" class="ml-2" style="color:#5cb85c;" onclick="handlePass(' + list[i].id + ')">通过</a>';
                        listHtml += '<a href="javascript:;" class="ml-2" style="color:#dc3545;" onclick="handleIllegal(' + list[i].id + ')">违规</a>';
                    }
                    listHtml += '</td>';
                    listHtml += '</tr>';
                }
                $('#comment-list').html(listHtml);
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
                var searchParam = assembleCommentSearchParam(p);
                renderCommentList(searchParam);
            });
        }

        function handleDelete(id)
        {
            handleDeleteCallback(function () {
                var param   = {id : id, status : -1}
                var data    = updateCommentField(param)
                if (data !== false) {
                    renderCommentList();
                }
            });
        }

        function handlePass(id)
        {
            var param   = {id : id, audit : 1}
            var data    = updateCommentField(param)
            if (data !== false) {
                renderCommentList();
            }
        }

        function handleIllegal(id)
        {
            var param   = {id : id, audit : -1}
            var data    = updateCommentField(param)
            if (data !== false) {
                renderCommentList();
            }
        }
    </script>
@endsection

