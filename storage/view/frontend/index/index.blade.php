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

        <div class="col-md-8 col-xs-6" style="padding-right: 0;">
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
            <!-- 作者 begin -->
            @include('frontend.layouts.author')
            <!-- 作者 end -->

            <!-- 标签 begin -->
            <div style="margin-top:20px;">
                <div class="card" >
                    <div class="card-body">
                        <h5>标签</h5>
                        <hr>
                        <div id="frontend-tags">

                        </div>
                    </div>
                </div>
            </div>
            <!-- 标签 end -->
        </div>
    </div>
</div>
<script>
    /**
     * 渲染文章列表
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
                listHtml += ' &nbsp;&nbsp; <i class="fa fa-comment"></i>&nbsp;';
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

    /**
     * 渲染标签列表
     */
    function renderTagList()
    {
        var data = listTag({}, false);
        if (data !== false) {
            $('#frontend-tags').html('');
            var listHtml = '';
            var list = data.list;
            listHtml += '<a href="javascript:;" class="mr-1"><span class="badge badge-primary tag-span tag-selected" onclick="handleSearch(1, -1)" id="tag-0">';
            listHtml += '全部';
            listHtml += '</span></a>';
            for (var i = 0; i < list.length; i++) {
                listHtml += '<a href="javascript:;" class="mr-1"><span class="badge badge-secondary tag-span" onclick="handleSearch(1, ' + list[i].id + ')" id="tag-' + list[i].id + '">';
                listHtml += list[i].name;
                listHtml += '</span></a>';
            }
            $('#frontend-tags').html(listHtml);
        }
    }

    // 渲染标签列表
    renderTagList();
    // 渲染文章列表
    renderArticleList();

    function handleSearch(p = 1, tagId = 0)
    {
        handleSearchCallback(function () {
            var keywords = $('#keywords').val().trim();
            var searchParam = {p : p};
            if (keywords !== '') {
                searchParam.keywords = keywords;
            }
            if (tagId === 0) {
                // 再看一下有没有选中的标签
                $('.tag-selected').each(function () {
                    idInfo = $(this).attr('id')
                    tagId = idInfo.split('-')[1];
                });
            }

            if (tagId < 0) {
                $('.tag-span').removeClass('badge-primary');
                $('.tag-span').removeClass('tag-selected');
                $('.tag-span').addClass('badge-secondary');

                $('#tag-0').removeClass('badge-secondary');
                $('#tag-0').addClass('badge-primary');
                $('#tag-0').addClass('tag-selected');
            }

            if (tagId > 0) {
                searchParam.tag_id = tagId;
                $('.tag-span').removeClass('badge-primary');
                $('.tag-span').removeClass('tag-selected');
                $('.tag-span').addClass('badge-secondary');

                $('#tag-' + tagId).removeClass('badge-secondary');
                $('#tag-' + tagId).addClass('badge-primary');
                $('#tag-' + tagId).addClass('tag-selected');
            }

            renderArticleList(searchParam);
        });
    }

</script>
@endsection