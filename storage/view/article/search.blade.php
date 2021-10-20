@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item active">文章</li>
@endsection
@section('content')
    <script src="/storage/js/validate/article.validate.js"></script>
    <script src="/storage/js/form/article.form.js"></script>

    <div class="card">
        <div class="card-header">
            <form id="user-search" onsubmit="return false;">
                <div class="row">

                    <div class="input-group-append mr-1">
                        <a href="/view/article/create"><button type="button" class="btn btn-block btn-outline-primary"><i class="fas fa-plus"></i></button></a>
                    </div>
                    <div class="col-3">
                        <input type="text" name="keywords" class="form-control" placeholder="标题或内容">
                    </div>
                    <div class="input-group-append ml-1">
                        <button type="submit" onclick="handleSearch();" class="btn btn-block btn-outline-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card-header -->
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width: 50px">ID</th>
                    <th style="width: 50px">排序</th>
                    <th style="width: 200px">标题</th>
                    <th style="width: 200px">内容</th>
                    <th style="width: 150px">创建时间</th>
                    <th style="width: 150px">更新时间</th>
                    <th style="width: 150px">操作</th>
                </tr>
                </thead>
                <tbody id="article-list">
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
        renderArticleList();

        /**
         * 渲染列表
         *
         * @param param
         */
        function renderArticleList(param = {})
        {
            var data = searchArticle(param);
            if (data !== false) {
                $('#article-list').html('');
                var listHtml = '';
                var list = data.list;
                for (var i = 0; i < list.length; i++) {
                    listHtml += '<tr class="expandable-body">';
                    listHtml += '<td>' + list[i].id + '</td>';
                    listHtml += '<td>' + list[i].sort + '</td>';
                    if (list[i].highlight_title == '') {
                        listHtml += '<td>' + cutString(list[i].title, 500) + '</td>';
                    } else {
                        listHtml += '<td>' + cutString(list[i].highlight_title, 500) + '</td>';
                    }
                    if (list[i].highlight_content == '') {
                        listHtml += '<td>' + cutString(list[i].content, 500) + '<div></td>';
                    } else {
                        listHtml += '<td><p>' + cutString(list[i].highlight_content, 500) + '</p></td>';
                    }
                    listHtml += '<td>' + list[i].ctime + '</td>';
                    listHtml += '<td>' + list[i].mtime + '</td>';
                    listHtml += '<td>';
                    listHtml += '<a href="/view/article/update?id=' + list[i].id + '"><i class="fas fa-edit"></i></a>';
                    listHtml += '<a href="javascript:;" class="ml-2" onclick="handleDelete(' + list[i].id + ')"><i class="fas fa-trash"></i></a>';
                    listHtml += '</td>';
                    listHtml += '</tr>';
                }
                $('#article-list').html(listHtml);
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
                var searchParam = assembleArticleSearchParam(p);
                renderArticleList(searchParam);
            });
        }

        function handleDelete(id)
        {
            handleDeleteCallback(function () {
                var param   = {id : id, status : -1}
                var data    = updateArticleField(param)
                if (data !== false) {
                    renderArticleList();
                }
            });
        }
    </script>
@endsection

