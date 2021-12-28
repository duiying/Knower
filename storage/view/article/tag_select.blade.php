<div class="form-check" id="tag-list" style="padding-left:0px;">

</div>

<script type="text/javascript">
    function renderTagList()
    {
        var data = selectTag();
        if (data !== false) {
            $('#tag-list').html('');
            var listHtml = '';
            var list = data.list;
            for (var i = 0; i < list.length; i++) {
                listHtml += '<input  type="checkbox" name="tag" id="tag-' + list[i].id + '" value="' + list[i].id + '">';
                listHtml += '<span class="font-weight-bold mr-1 ml-1">' + list[i].name + '</span>';
            }
            $('#tag-list').html(listHtml);
        }
    }

    // 渲染列表
    renderTagList();
</script>