@extends('frontend.layouts.app')
@section('content')
    <div class="container" style="margin-top:20px;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header">登录</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <a class="btn btn-outline-secondary" href="/oauth/qq">
                                    <img src="/storage/frontend/img/qq_logo.png" style="width:14px;margin-bottom:3px;">&nbsp;QQ 登录
                                </a>
                                <br>
                                <a class="btn btn-outline-secondary" href="/oauth/github" style="margin-top:5px;">
                                    <img src="/storage/frontend/img/github_logo.png" style="width:14px;margin-bottom:3px;">&nbsp;GitHub 登录
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    </script>
@endsection