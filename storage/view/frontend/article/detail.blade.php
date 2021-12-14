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
                            <img id="zanshangImg" style="display: none;text-align: center;" width="300px;" src="/storage/frontend/img/wechat_zanshang.jpeg">
                        </p>
                    </div>
                </div>
                <div class="card" style="margin-top:15px;">
                    <div class="card-body">
                        <h3 id="commentHead">评论</h3>
                        <hr>
                        <table id="commentList" style="margin:10px 0 20px 0;">
                            @foreach($article->comments as $comment)
                                <tr>
                                    <td style="padding: 0 10px;">
                                        <a name="{{ $comment->username }}" href="{{ $comment->website }}"
                                           target="_blank">
                                            <img style="height:35px;border-radius:50%;"
                                                 src="{{ ($comment->email==$admin->email)? $admin->avatar:Identicon::getImageDataUri($comment->username) }}">
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ $comment->website }}"
                                           target="_blank">{{ $comment->username }}</a>
                                        <br>
                                        <span style="color:#ddd;">{{ $comment->created_at->format('Y-m-d H:i:s') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <p style="padding: 0 0 15px 0;" id="comment-content-{{ $comment->id }}">
                                            <textarea style="display:none;"> {!! $comment->content !!} </textarea>
                                        </p>
                                </tr>
                            @endforeach
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
                                <button id="commentSubmit" class="btn btn-primary">
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
                        <div id="menuContent"></div>
                        <div>
                            <br>
                            <a id="topComment" style="color:#505050;"  href="javascript:void(0);"><i class="fa fa-bookmark-o"></i>&nbsp;·&nbsp;跳到评论</a>
                            <br>
                            <a id="topMao" style="color:#505050;"  href="javascript:void(0);"><i class="fa fa-bookmark-o"></i>&nbsp;·&nbsp;跳到顶部</a>
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
        var data = detailArticle({id : $('input[name=id]').val()});
        if (data !== false) {
            $('#article-title').html(data.title);
            $('#article-content').html(data.content);
        }

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
            $('#topMao').click(function () {
                $("html,body").animate({scrollTop: $("#app").offset().top}, 500);
            });
            $('#topComment').click(function () {
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

            editormd.markdownToHTML("comment-content-1", {
                htmlDecode: "style,script,iframe",
                emoji: true,
                taskList: true,
                tex: true,
                flowChart: false,
                sequenceDiagram: false,
                codeFold: true,
            });

            $("#doc-content").find("h2,h3,h4,h5,h6").each(function(i,item){
                var tag = $(item).get(0).localName;
                $(item).attr("id","wow"+i);
                $("#menuContent").append('<div><a style="color:#505050;" class="new'+tag+' anchor-link" onclick="return false;" href="#" link="#wow'+i+'">'+(i+1)+" · "+$(this).text()+'</a></div>');
                $(".newh2").css("margin-left",0);
                $(".newh3").css("margin-left",20);
                $(".newh4").css("margin-left",40);
                $(".newh5").css("margin-left",60);
                $(".newh6").css("margin-left",80);
            });

            $(".anchor-link").click(function(){
                $("html,body").animate({scrollTop: $($(this).attr("link")).offset().top}, 1000);
            });

            $('#commentSubmit').click(function () {
                $('#content').removeClass('is-invalid');
                $('#content').next('.invalid-feedback').remove();

                $.ajax({
                    url: "/",
                    type: 'post',
                    data: $('#commentForm').serializeArray(),
                    success: function (data) {
                        $('#captchaImg').attr('src', $('#captchaImg').attr('src') + Math.random());
                        $('#captcha').val('');
                        var avatar='/storage/frontend/img/default_avatar.png'

                        $('#commentList').append('<tr>' +
                            '                                    <td style="padding: 0 10px;">' +
                            '                                        <a  href="'+data.website+'"' +
                            '                                           target="_blank">' +
                            '                                            <img style="border-radius:50%;"' +
                            '                                                 src="'+avatar+'">' +
                            '                                        </a>' +
                            '                                    </td>' +
                            '                                    <td>' +
                            '                                        <a href="'+data.website+'"' +
                            '                                           target="_blank">'+data.username+'</a>' +
                            '                                        <br>' +
                            '                                        <span style="color:#ddd;">'+data.created_at+'</span>' +
                            '                                    </td>' +
                            '                                </tr>' +
                            '                                <tr>' +
                            '                                    <td></td>' +
                            '                                    <td>' +
                            '                                        <p style="padding: 0 0 15px 0;" id="comment-content-'+data.id+'">' +
                            '                                            <textarea style="display:none;"> '+data.content+' </textarea>' +
                            '                                        </p>' +
                            '                                </tr>');
                        editormd.markdownToHTML("comment-content-"+data.id, {
                            htmlDecode: "style,script,iframe",
                            emoji: true,
                            taskList: true,
                            tex: true,
                            flowChart: false,
                            sequenceDiagram: false,
                            codeFold: true,
                        });
                        alert('评论成功');
                        $("html,body").animate({scrollTop: $("#comment-content-"+data.id).offset().top-80}, 300);

                    },
                    error: function (xhr, status, error) {
                        $('#captchaImg').attr('src', $('#captchaImg').attr('src') + Math.random());
                        var errors = xhr.responseJSON.errors;
                        for (var key in errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).after('<span class="invalid-feedback" style="display: block;">' +
                                ' <strong>' + errors[key][0] +
                                '</strong>' +
                                ' </span>');
                        }
                    }
                });

            });

        });
    </script>
@endsection