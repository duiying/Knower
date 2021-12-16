@extends('frontend.layouts.app')
@section('content')
    <input name="id" type="hidden" value="{{ $id }}">
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
                            如果这篇文章帮助到了您，可以赞助下主机费~~<br>
                        </p>
                        <p class="text-center">
                            <button class="btn btn-success" id="zanshang">赞赏</button>
                            <br>
                            <img id="zanshangImg" style="display: none;text-align: center;" width="300px;" src="/storage/frontend/img/wechat_zanshang.png">
                        </p>
                    </div>
                </div>
                <div class="card" style="margin-top:15px;">
                    <div class="card-body">
                        <h3 id="commentHead">评论</h3>
                        <hr>
                        <table id="commentList" style="margin:10px 0 20px 0;">

                        </table>
                        <form id="commentForm" onsubmit="return false;">
                            <div class="form-group">
                                <label for="content"
                                       class="form-label"><span class="text-danger" style="font-size:20px;">*
                                    </span>评论内容，支持
                                    <a href="https://markdown.com.cn/basic-syntax/" target="_blank">Markdown</a>
                                </label>
                                <textarea id="content" rows="6" class="form-control" name="content" required>内容</textarea>
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

    <script src="/storage/frontend/editormd/editormd.min.js"></script>
    <script src="/storage/frontend/markdown/marked.min.js"></script>
    <script src="/storage/frontend/markdown/prettify.min.js"></script>
    <script type="text/javascript">
        articleId = $('input[name=id]').val();

        // 渲染文章数据
        function renderArticleData()
        {
            var data = detailArticle({id : articleId});
            if (data !== false) {
                $('#article-title').html(data.title);
                $('#article-content').html(data.content);
            }
        }

        commentList = comments({third_id : articleId});
        // 渲染评论数据
        function renderCommentData()
        {
            var listHtml = '';
            if (commentList !== false) {
                var list = commentList.list;
                for (var i = 0; i < list.length; i++) {
                    listHtml += '<tr>';
                    listHtml += '<td style="padding: 0 10px;">';
                    listHtml += '<a href="javascript:;">';
                    listHtml += '<img style="height:35px;border-radius:50%;" src="' + list[i].account_info.avatar + '">';
                    listHtml += '</a>';
                    listHtml += '</td>';
                    listHtml += '<td>';
                    listHtml += '<a href="javascript:;">' + list[i].account_info.nickname + '</a><br>'
                    listHtml += '<span style="color:#ddd;">' + list[i].ctime + '</span>'
                    listHtml += '</td></tr>';
                    listHtml += '<tr><td></td>';
                    listHtml += '<td>';
                    listHtml += '<p style="padding: 0 0 15px 0;" id="comment-content-' + list[i].id + '">';
                    listHtml += '<textarea style="display:none;">' + list[i].content + '</textarea>';
                    listHtml += '</p></td></tr>';
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

        $(function () {
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

            $("#doc-content").find("h2,h3,h4,h5,h6").each(function(i, item) {
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

            $('#comment-submit').click(function () {
                
            });
        });
    </script>
@endsection