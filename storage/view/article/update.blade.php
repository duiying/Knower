@extends('layouts.app')
@section('header')
    <li class="breadcrumb-item"><a href="/view/article/search">文章</a></li>
    <li class="breadcrumb-item active">更新</li>
@endsection
@section('content')
    <script src="/storage/js/validate/article.validate.js"></script>
    <script src="/storage/js/form/article.form.js"></script>

    <input name="id" type="hidden" value="{{ $id }}">

    <div class="row">
        <div class="col-12">
            <!-- /.card -->
            <!-- Horizontal Form -->
            <div class="card card-info">
                <!-- /.card-header -->
                <!-- form start -->
                <form class="form-horizontal" id="article-update" onsubmit="return false;">
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
                                    <span class="input-group-text" style="font-size: 0.85rem;hover" id="upload-file">Upload</span>
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
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">内容<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea name="content" class="form-control" id="article-markdown" rows="10"></textarea>
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

        var data = findArticle({id : $('input[name=id]').val()});

        if (data !== false) {
            // 渲染表单数据
            renderUpdateForm(data, ['title', 'desc', 'sort']);
            if (data !== false) {
                simplemde.value(data.content);
            }
            if (data.cover_img_id !== 0) {
                $('#cover_img_id').attr('value', data.id);
            }
            if (data.cover_img_url !== '') {
                $('#cover-img').removeClass('display-none');
                $('#cover-img-url').attr('src', data.cover_img_url);
                $('.custom-file-label').html(data.filename);
            }
        }

        $('input[type="file"]').change(function(e) {
            var fileName = e.target.files[0].name;
            $('.custom-file-label').html(fileName);
        });

        function handleSubmit()
        {
            if (validateArticleParam('article-update')) {
                var param   = assembleArticleFormParam(true)
                var data    = updateArticle(param)
                if (data !== false) {
                    pjaxToUrl('/view/article/search');
                }
            }
        }
    </script>
@endsection

