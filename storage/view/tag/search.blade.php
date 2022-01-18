@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item"><a href="/admin">首页</a></li>
    <li class="breadcrumb-item active">标签</li>
@endsection
@section('content')
    <script src="/storage/js/validate/tag.validate.js"></script>
    <script src="/storage/js/form/tag.form.js"></script>

    <div class="card">
        <div class="card-header">
            <form id="tag-search" onsubmit="return false;">
                <div class="row">

                    <div class="input-group-append mr-1">
                        <a href="/view/tag/create"><button type="button" class="btn btn-block btn-outline-primary"><i class="fa fa-plus"></i></button></a>
                    </div>
                    <div class="col-3">
                        <input type="text" name="name" class="form-control" placeholder="名称">
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
                    <th style="width: 50px">排序</th>
                    <th style="width: 150px">名称</th>
                    <th style="width: 150px">创建时间</th>
                    <th style="width: 150px">更新时间</th>
                    <th style="width: 150px">操作</th>
                </tr>
                </thead>
                <tbody id="tag-list">
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
        renderTagList();

        /**
         * 渲染列表
         *
         * @param param
         */
        function renderTagList(param = {})
        {
            var data = searchTag(param);
            if (data !== false) {
                $('#tag-list').html('');
                var listHtml = '';
                var list = data.list;
                for (var i = 0; i < list.length; i++) {
                    listHtml += '<tr>';
                    listHtml += '<td>' + list[i].id + '</td>';
                    listHtml += '<td>' + list[i].sort + '</td>';
                    listHtml += '<td>' + list[i].name + '</td>';
                    listHtml += '<td>' + list[i].ctime + '</td>';
                    listHtml += '<td>' + list[i].mtime + '</td>';
                    listHtml += '<td>';
                    listHtml += '<a href="/view/tag/update?id=' + list[i].id + '"><i class="fa fa-edit"></i></a>';
                    if (list[i].root != 1) {
                        listHtml += '<a href="javascript:;" class="ml-2" onclick="handleDelete(' + list[i].id + ')"><i class="fa fa-trash"></i></a>';
                    }
                    listHtml += '</td>';
                    listHtml += '</tr>';
                }
                $('#tag-list').html(listHtml);
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
                var searchParam = assembleTagSearchParam(p);
                renderTagList(searchParam);
            });
        }

        function handleDelete(id)
        {
            handleDeleteCallback(function () {
                var param   = {id : id, status : -1}
                var data    = deleteTag(param)
                if (data !== false) {
                    renderTagList();
                }
            });
        }
    </script>
@endsection

