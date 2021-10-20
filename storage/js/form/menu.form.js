/**
 * 组装表单参数
 *
 * @returns {{password: (jQuery|string|undefined), name: (jQuery|string|undefined), mobile: (jQuery|string|undefined), position: (jQuery|string|undefined), email: (jQuery|string|undefined)}}
 */
function assembleMenuFormParam(fromUpdate = false)
{
    var pid     = $('select[name=pid]').val();
    var name    = $('input[name=name]').val();
    var icon    = $('input[name=icon]').val();
    var url     = $('input[name=url]').val();
    var sort    = $('input[name=sort]').val();

    var retFromParam = {
        pid : pid,
        name : name,
        icon : icon,
        url : url,
        sort : sort,
    }

    if (fromUpdate) {
        retFromParam.id = $('input[name=id]').val();
    }

    return retFromParam;
}

function renderMenuPidSelect()
{
    $(function () {$('.select2').select2()})
    // 上级菜单下拉选择
    var pidSelectList = searchMenu({pid : 0})
    if (pidSelectList !== false) {
        renderSelect('pid-select', pidSelectList.list, 'id', 'name', '<option value="0">根节点</option>')
    }
    $('#form-group-url').addClass('none');
    $('#pid-select').change(function () {
        var pid = $('#pid-select option:selected').val();
        if (pid == 0) {
            $('#form-group-url').addClass('none');
        } else {
            $('#form-group-url').removeClass('none');
        }
    });
}