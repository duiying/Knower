@extends('frontend.layouts.app')
@section('content')
    <input name="id" type="hidden" value="{{ $id }}">
    <input name="comment_id" type="hidden" value="{{ $comment_id }}">
    <div class="container" style="margin-top:20px;">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div style="margin:10px 20px;">
                            <h2 id="article-title">

                            </h2>
                        </div>

                        <p id="doc-content">
                            <textarea style="display:none;" id="article-content">  </textarea>
                        </p>
                        <p class="text-center" style="margin-top:20px;">
                            如果这篇文章帮助到了您，可以赞助下服务器费~~<br>
                        </p>
                        <p class="text-center">
                            <button class="btn btn-success" id="zanshang">赞赏</button>
                            <br>
                            <img id="zanshangImg" style="display: none;text-align: center;" width="300px;" src="/storage/frontend/img/wechat_zanshang.png">
                        </p>
                    </div>
                </div>
                <hr>
                <div class="text-center" style="margin-top:15px;" id="commentHead">评论数量：<span id="comment-total">0</span></div>

                <div id="commentList">

                </div>
                <div class="clearfix"></div>
                <div class="card" style="margin-top:15px;margin-bottom:80px;">
                    <div class="card-body">
                        <form id="commentForm" onsubmit="return false;">
                            <div class="form-group">
                                <label for="content"
                                       class="form-label"><span class="text-danger" style="font-size:20px;">*
                                    </span>评论内容，支持
                                    <a href="https://markdown.com.cn/basic-syntax/" target="_blank">Markdown</a>
                                </label>
                                <textarea id="comment-content" rows="6" class="form-control" name="content" required></textarea>
                            </div>
                            <div class="form-group">
                                <button id="comment-submit" class="btn btn-primary">
                                    提交
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="padding-left:0;position: relative;">
                <div class="card" id="menu" style="position: fixed;overflow: auto;width:270px;">
                    <div class="card-body">
                        <h3>目录</h3>
                        <hr>
                        <div id="menu-content"></div>
                        <div>
                            <br>
                            <a id="jumpToComment" style="color:#505050;"  href="javascript:void(0);"><i class="fa fa-bookmark-o"></i>&nbsp;·&nbsp;跳到评论</a>
                            <br>
                            <a id="jumpToTop" style="color:#505050;"  href="javascript:void(0);"><i class="fa fa-bookmark-o"></i>&nbsp;·&nbsp;跳到顶部</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        articleId = parseInt($('input[name=id]').val());
        commentId = parseInt($('input[name=comment_id]').val());

        // 渲染文章数据
        function renderArticleData()
        {
            var data = detailArticle({id : articleId});
            if (data !== false) {
                $('#article-title').html(data.title);
                if (data.cached_content !== '') {
                    $('#article-content').html(data.cached_content);
                } else {
                    $('#article-content').html(data.content);
                }
            }
        }

        commentList = comments({third_id : articleId});
        // 渲染评论数据
        function renderCommentData()
        {
            var listHtml = '';
            if (commentList !== false) {
                $('#comment-total').html(commentList.total);
                var list = commentList.list;
                for (var i = 0; i < list.length; i++) {
                    listHtml += '<div style="margin-top:15px;padding:20px 20px 0px;background-color:#fff;" class="card">';
                    listHtml += '<div>';
                    listHtml += '<div class="float-left">';
                    listHtml += '<img style="height:35px;border-radius:50%;" src="' + list[i].account_info.avatar + '">';
                    listHtml += '</div>';
                    listHtml += '<div class="col-md-11 float-left">';
                    listHtml += '<p style="margin: 0px;">' + list[i].account_info.nickname + '</p>';
                    listHtml += '<p style="margin:0px;color:#ddd;">' + list[i].format_ctime + '</p>';
                    listHtml += '</div>';
                    listHtml += '</div>';
                    listHtml += '<div class="clearfix"></div>';
                    listHtml += '<div class="col-md-12 mt-1">';
                    listHtml += '<p id="comment-content-' + list[i].id + '" style="padding:0;">';
                    listHtml += '<textarea style="display:none;">' + list[i].content + '</textarea>';
                    listHtml += '</p>';
                    listHtml += '</div>'
                    listHtml += '</div>';
                    listHtml += '<div></div>';
                }
            }
            $('#commentList').html(listHtml);
        }

        renderArticleData();
        renderCommentData();

        var tit = document.getElementById('menu');
        var titleTop = tit.offsetTop;
        // 滚动事件
        document.onscroll = function () {
            // 获取当前滚动的距离
            var btop = document.body.scrollTop || document.documentElement.scrollTop;
            // 如果滚动距离大于导航条据顶部的距离
            if (btop >= titleTop) {
                tit.style.top = "0px";
            } else {
                tit.style.top = (titleTop - btop) + 'px';
            }
        }

        $('#jumpToTop').click(function () {
            $("html,body").animate({scrollTop: $("#app").offset().top}, 500);
        });
        $('#jumpToComment').click(function () {
            $("html,body").animate({scrollTop: $("#commentHead").offset().top}, 500);
        });

        $('#zanshang').click(function () {
            $("#zanshangImg").toggle(500);
        });

        editormd.markdownToHTML("doc-content", {
            htmlDecode: "style,script,iframe",
            emoji: true,
            taskList: true,
            tex: true,
            flowChart: false,
            sequenceDiagram: false,
            codeFold: true,
        });

        if (commentList !== false) {
            var list = commentList.list;
            for (var i = 0; i < list.length; i++) {
                editormd.markdownToHTML("comment-content-" + list[i].id, {
                    htmlDecode: "style,script,iframe",
                    emoji: true,
                    taskList: true,
                    tex: true,
                    flowChart: false,
                    sequenceDiagram: false,
                    codeFold: true,
                });
            }
        }

        $("#doc-content").find("h1,h2,h3,h4,h5,h6").each(function(i, item) {
            var tag = $(item).get(0).localName;
            $(item).attr("id", "wow" + i);
            $("#menu-content").append('<div><a style="color:#505050;" class="new' + tag + ' anchor-link" onclick="return false;" href="#" link="#wow' + i + '">' + (i + 1) + " · " + $(this).text() + '</a></div>');
            $(".newh2").css("margin-left", 0);
            $(".newh3").css("margin-left", 20);
            $(".newh4").css("margin-left", 40);
            $(".newh5").css("margin-left", 60);
            $(".newh6").css("margin-left", 80);
        });

        $(".anchor-link").click(function() {
            $("html,body").animate({scrollTop: $($(this).attr("link")).offset().top}, 1000);
        });

        if (commentId !== 0) {
            $("html,body").animate({scrollTop: $("#comment-content-" + commentId).offset().top - 80}, 300);
        }

        $('#comment-submit').click(function () {
            var createCommentParams = {
                third_id : articleId,
                content : $('#comment-content').val()
            };
            var data = createComment(createCommentParams);
            if (data !== false) {
                alertSuccess('评论成功！');
                setTimeout(function () {
                    location.href = '/article/detail?id=' + articleId + '&comment_id=' + data;
                }, 1500);
            } else {
                alertError('评论失败！');
            }
        });
    </script>
@endsection