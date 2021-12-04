@extends('frontend.layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" style="padding-right: 0;">
            <div class="card" style="margin-top:20px;">
                <div class="card-body" id="frontend-articles">

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
    // 渲染列表
    renderArticleList();

    /**
     * 渲染列表
     */
    function renderArticleList()
    {
        var data = listArticle();
        if (data !== false) {
            $('#frontend-articles').html('');
            var listHtml = '';
            var list = data.list;
            for (var i = 0; i < list.length; i++) {
                listHtml += '<h5 style="padding-top:10px;padding-bottom:5px;"><strong><a href="/article/detail?id=' + list[i].id + '">';
                listHtml +=  list[i].title;
                listHtml += '</a></strong></h5>';
                listHtml += '<div class="row"><div class="col-md-12"><div style="margin-bottom: 5px;"><a href="">';
                listHtml += list[i].desc;
                listHtml += '</a></div><div><i class="fa fa-clock-o"></i>&nbsp;';
                listHtml += list[i].mtime;
                listHtml += ' &nbsp;&nbsp;<i class="fa fa-eye"></i>&nbsp;';
                listHtml += list[i].read_count;;
                listHtml += ' &nbsp;&nbsp; <i class="fa fa-comments-o"></i>&nbsp;';
                listHtml += '30';
                listHtml += '</div></div></div><hr>';
            }
            $('#frontend-articles').html(listHtml);
        }
    }
</script>
@endsection