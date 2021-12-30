@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item"><a href="/admin">首页</a></li>
    <li class="breadcrumb-item"><a href="/view/article/search">文章</a></li>
    <li class="breadcrumb-item active">创建</li>
@endsection
@section('content')
    <script src="/storage/js/validate/article.validate.js"></script>
    <script src="/storage/js/form/article.form.js"></script>

    <div class="row">
        <div class="col-12">
            <!-- /.card -->
            <!-- Horizontal Form -->
            <div class="card card-info">
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" id="article-create" onsubmit="return false;">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">标题<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="title" class="form-control" placeholder="标题">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">描述<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="desc" class="form-control" placeholder="描述">
                            </div>
                        </div>
                        <div class="form-group row" id="upload-file-block">
                            <label class="col-sm-2 col-form-label">封面图</label>
                            <div class="input-group col-sm-10">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="inputFile">
                                    <label class="custom-file-label" for="inputFile">请上传封面图</label>
                                </div>
                                <div class="input-group-append">
                                    <span class="input-group-text" style="font-size: 0.85rem;" id="upload-file">Upload</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row display-none">
                            <div class="col-sm-12">
                                <input type="text" id="cover_img_id" value="0">
                            </div>
                        </div>
                        <div class="form-group row display-none" id="cover-img">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-10">
                                <img src="/public/1.png" style="max-height: 200px;" id="cover-img-url">
                            </div>
                        </div>
                        <script type="application/javascript">
                            $('input[type="file"]').change(function(e) {
                                var fileName = e.target.files[0].name;
                                $('.custom-file-label').html(fileName);
                            });
                        </script>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">内容<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea name="content" class="form-control" id="article-markdown" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">标签</label>
                            <div class="col-sm-10">
                                <!-- 标签 begin -->
                                @include('article.tag_select')
                                <!-- 标签 end -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">排序（升序）</label>
                            <div class="col-sm-10">
                                <input type="text" name="sort" class="form-control" placeholder="排序" value="99">
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right" onclick="handleSubmit();">提交</button>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>

    <script type="text/javascript">
        var simplemde = getSimpleMDE("article-markdown");

        $('#inputFile, #upload-file').mouseover(function () {
            $(this).css('cursor', 'pointer');
        });

        $('#upload-file').click(function () {
            var formData = new FormData();
            formData.append('file', $('#inputFile')[0].files[0]);
            $.ajax({
                type: "POST",
                url: "/v1/img/upload",
                cache: false,
                processData: false,
                contentType: false,
                data: formData ,
                async:false,
                success: function (resp) {
                    if (resp.code !== 0) {
                        $('#cover-img').addClass('display-none');
                        alert.error(resp.msg);
                    } else {
                        $('#cover-img').removeClass('display-none');
                        $('#cover-img-url').attr('src', resp.data.cover_img);
                        $('#cover_img_id').attr('value', resp.data.id);
                        alert.success('上传成功！');
                    }
                }
            });
        });
        
        function handleSubmit()
        {
            if (validateArticleParam('article-create')) {
                var param   = assembleArticleFormParam()
                var data    = createArticle(param)
                if (data !== false) {
                    pjaxToUrl('/view/article/search');
                }
            }
        }
    </script>
@endsection

