<div class="card" style="margin-top:20px;">
    <div class="card-body">
        <h5>关注</h5>
        <hr>
        <div style="text-align: center;">
            <img style="width:35%;border-radius:50%;" src="/storage/frontend/img/author_avatar.jpeg">
            <div style="margin-bottom:5px;"><strong>对影</strong></div>
            努力，专注<br>
            <a target="_blank" href="https://github.com/duiying">GitHub</a>&nbsp;|&nbsp;
            <a href="javascript:;" id="wechat">微信</a>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#wechat').click(function () {
            Swal.fire({
                text: '有任何问题都可以加我微信哦~',
                imageUrl: '/storage/frontend/img/wechat.jpeg',
                imageWidth: 300,
                confirmButtonColor: '#3085d6',
            })
        });
    });
</script>