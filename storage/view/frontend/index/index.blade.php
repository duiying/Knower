@extends('frontend.layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12" style="margin-top:20px;">
            <form action="/" class="form-inline" id="article-search" onsubmit="return false;">
                <input class="form-control col-md-4" type="text" id="keywords" placeholder="请输入搜索内容" value="">
                <button class="btn btn-outline-secondary ml-2" type="submit" onclick="handleSearch();"><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
        </div>

        <div class="col-md-8" style="padding-right: 0;">
            <div class="card" style="margin-top:20px;">
                <div class="card-body" id="frontend-articles">

                </div>
                <div class="card-footer clearfix" id="pagination">
                    <div class="float-left" id="pagination-total"></div>
                    <ul class="pagination pagination-sm m-0 float-right">

                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="padding-left:20px;">
            <div class="card" style="margin-top:20px;">
                <div class="card-body">
                    <h5>关注</h5>
                    <hr>

                </div>
            </div>

            <div style="margin-top:20px;">
                <!-- 标签 begin -->
                @include('frontend.layouts.tag')
                <!-- 标签 end -->
            </div>
        </div>
    </div>
</div>
<script>
    /**
     * 渲染列表
     */
    function renderArticleList(searchParam = {})
    {
        var data = listArticle(searchParam);
        if (data !== false) {
            $('#frontend-articles').html('');
            var listHtml = '';
            var list = data.list;
            for (var i = 0; i < list.length; i++) {
                listHtml += '<h5 style="padding-top:10px;padding-bottom:5px;"><strong><a href="/article/detail?id=' + list[i].id + '">';
                if (list[i].highlight_title == '') {
                    listHtml +=  list[i].title;
                } else {
                    listHtml +=  list[i].highlight_title;
                }
                listHtml += '</a></strong></h5>';
                listHtml += '<div class="row">';
                if (list[i].cover_img_url === '') {
                    listHtml += '<div class="col-md-12">';
                } else {
                    listHtml += '<div class="col-md-8">';
                }
                listHtml += '<div style="margin-bottom: 5px;"><a href="/article/detail?id=' + list[i].id + '">';
                if (list[i].highlight_desc == '') {
                    listHtml += list[i].desc;
                    if (list[i].highlight_desc === '' && list[i].highlight_title === '' && searchParam.keywords !== undefined) {
                        listHtml += '<span style="color:#ccc;">（内容中含有<span><code>' + searchParam.keywords + '</code><span style="color:#ccc;">关键词）</span>';
                    }
                } else {
                    listHtml += list[i].highlight_desc;
                }

                listHtml += '</a></div><div><i class="fa fa-clock-o"></i>&nbsp;';
                listHtml += list[i].mtime;
                listHtml += ' &nbsp;&nbsp;<i class="fa fa-eye"></i>&nbsp;';
                listHtml += list[i].read_count;
                listHtml += ' &nbsp;&nbsp; <i class="fa fa-comments-o"></i>&nbsp;';
                listHtml += list[i].comment_count;
                listHtml += '</div></div>';
                if (list[i].cover_img_url !== '') {
                    listHtml += '<div class="col-md-4">';
                    listHtml += '<a href="">';
                    listHtml += '<img style="width:100%;" src="' + list[i].cover_img_url + '">';
                    listHtml += '</a>';
                    listHtml += '</div>';
                }
                listHtml += '</div><hr>';
            }
            if (listHtml === '') {
                listHtml += '<h6 class="text-center">暂无数据~</h6>';
            }
            $('#frontend-articles').html(listHtml);
        }
        renderPage(data);
    }

    // 渲染列表
    renderArticleList();

    /**
     * 搜索处理
     *
     * @param p 页码
     */
    function handleSearch(p = 1)
    {
        handleSearchCallback(function () {
            var keywords = $('#keywords').val().trim();
            var searchParam = {p : p};
            if (keywords !== '') {
                searchParam.keywords = keywords;
            }
            renderArticleList(searchParam);
        });
    }

</script>
@endsection