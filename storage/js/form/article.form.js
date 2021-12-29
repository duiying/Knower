/**
 * 组装表单参数
 *
 * @returns {{password: (jQuery|string|undefined), name: (jQuery|string|undefined), mobile: (jQuery|string|undefined), position: (jQuery|string|undefined), email: (jQuery|string|undefined)}}
 */
function assembleArticleFormParam(fromUpdate = false)
{
    var title           = $('input[name=title]').val();
    var desc            = $('input[name=desc]').val();
    var sort            = $('input[name=sort]').val();
    var cover_img_id    = $('#cover_img_id').val();
    var tag_ids         = '';
    $('input[name="tag"]').each(function () {
        if ($(this).is(':checked')) {
            tag_ids += $(this).attr('value') + ',';
        }
    });
    if (tag_ids !== '') {
        tag_ids = tag_ids.slice(0, -1);
    }
    console.log(tag_ids)

    var retFormParam = {
        title           : title,
        desc            : desc,
        sort            : sort,
        cover_img_id    : cover_img_id,
        content         : simplemde.value(),
        tag_ids         : tag_ids
    }

    if (fromUpdate) {
        retFormParam.id = $('input[name=id]').val();
    }

    return retFormParam;
}

function assembleArticleSearchParam(p)
{
    var keywords = $('input[name=keywords]').val();
    var id = $('input[name=id]').val();

    var searchParam = {
        p       : DEFAULT_P,
        size    : DEFAULT_SIZE,
    };
    if (p !== 0) searchParam.p = p;
    if (id !== '') searchParam.id = id;

    if (keywords !== '') searchParam.keywords = keywords;

    return searchParam;
}