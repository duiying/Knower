<div style="margin-top:20px;">
    <div class="card" >
        <div class="card-body">
            <h5>标签</h5>
            <hr>
            <div id="frontend-tags">
                <a href=""><span class="badge badge-secondary">PHP</span></a>
            </div>
        </div>
    </div>
</div>
<script>
    // 渲染列表
    renderTagList();

    /**
     * 渲染列表
     */
    function renderTagList()
    {
        var data = listTag({}, false);
        if (data !== false) {
            $('#frontend-tags').html('');
            var listHtml = '';
            var list = data.list;
            for (var i = 0; i < list.length; i++) {
                listHtml += '<a href="" class="mr-1"><span class="badge badge-secondary">';
                listHtml +=  list[i].name;
                listHtml += '</span></a>';
            }
            $('#frontend-tags').html(listHtml);
        }
    }
</script>