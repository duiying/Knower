/**
 * 组装表单参数
 *
 * @param fromUpdate
 * @returns {{name: *|Window.jQuery|string, sort: *|Window.jQuery|string}}
 */
function assembleTagFormParam(fromUpdate = false)
{
    var name        = $('input[name=name]').val();
    var sort        = $('input[name=sort]').val();
    var retFromParam = {
        name : name,
        sort : sort,
    }

    if (fromUpdate) {
        retFromParam.id = $('input[name=id]').val();
    }

    return retFromParam;
}

function assembleTagSearchParam(p)
{
    var name        = $('input[name=name]').val();

    var searchParam = {
        p       : DEFAULT_P,
        size    : DEFAULT_SIZE,
    };
    if (p !== 0) searchParam.p = p;

    if (name !== '')        searchParam.name = name;

    return searchParam;
}