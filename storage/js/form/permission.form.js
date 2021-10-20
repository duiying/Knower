/**
 * 组装表单参数
 *
 * @returns {{password: (jQuery|string|undefined), name: (jQuery|string|undefined), mobile: (jQuery|string|undefined), position: (jQuery|string|undefined), email: (jQuery|string|undefined)}}
 */
function assemblePermissionFormParam(fromUpdate = false)
{
    var name    = $('input[name=name]').val();
    var url     = $('textarea[name=url]').val();
    var sort    = $('input[name=sort]').val();

    var retFromParam = {
        name : name,
        url : url,
        sort : sort,
    }

    if (fromUpdate) {
        retFromParam.id = $('input[name=id]').val();
    }

    return retFromParam;
}