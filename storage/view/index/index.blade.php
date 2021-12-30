@extends('layouts.app')
@section('content')
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="account-count">0</h3>
                            <p>用户数</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="/view/account/search" class="small-box-footer">更多 <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="article-count">0</h3>
                            <p>文章数</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="/view/article/search" class="small-box-footer">更多 <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="comment-count">0</h3>
                            <p>评论数</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="/view/comment/search" class="small-box-footer">更多 <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <!-- Main row -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">数据分析</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                <tr>
                                    <th>指标</th>
                                    <th>本日</th>
                                    <th>本周</th>
                                    <th>本月</th>
                                    <th>本年</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            活跃登录用户
                                        </td>
                                        <td id="today-account">0</td>
                                        <td id="week-account">0</td>
                                        <td id="month-account">0</td>
                                        <td id="year-account">0</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            活跃游客数
                                        </td>
                                        <td id="today-tourist">0</td>
                                        <td id="week-tourist">0</td>
                                        <td id="month-tourist">0</td>
                                        <td id="year-tourist">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content -->

    <script>
        var data = statData();
        if (data !== false) {
            $('#account-count').html(data.account_count);
            $('#article-count').html(data.article_count);
            $('#comment-count').html(data.comment_count);

            $('#today-account').html(data.today_account_count);
            $('#week-account').html(data.week_account_count);
            $('#month-account').html(data.month_account_count);
            $('#year-account').html(data.year_account_count);

            $('#today-tourist').html(data.today_tourist_count);
            $('#week-tourist').html(data.week_tourist_count);
            $('#month-tourist').html(data.month_tourist_count);
            $('#year-tourist').html(data.year_tourist_count);
        }
    </script>
@endsection

