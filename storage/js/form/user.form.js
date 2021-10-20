/**
 * 组装表单参数
 *
 * @returns {{password: (jQuery|string|undefined), name: (jQuery|string|undefined), mobile: (jQuery|string|undefined), position: (jQuery|string|undefined), email: (jQuery|string|undefined)}}
 */
function assembleUserFormParam(fromUpdate = false)
{
    var name        = $('input[name=name]').val();
    var mobile      = $('input[name=mobile]').val();
    var email       = $('input[name=email]').val();
    var position    = $('input[name=position]').val();
    var password    = $('input[name=password]').val();
    var role_id  = '';
    var roleOptions = $('#roleSelect option:selected');
    if (roleOptions.length > 0) {
        for (var i = 0; i < roleOptions.length; i++) {
            role_id += roleOptions[i].value + ',';
        }
    }

    var retFromParam = {
        name : name,
        mobile : mobile,
        email : email,
        position : position,
        password : password,
    }

    if (role_id !== '') {
        retFromParam.role_id = role_id.substr(0, role_id.length - 1);
    }

    if (fromUpdate) {
        retFromParam.id = $('input[name=id]').val();
    }

    return retFromParam;
}

function assembleUserSearchParam(p)
{
    var name        = $('input[name=name]').val();
    var mobile      = $('input[name=mobile]').val();
    var email       = $('input[name=email]').val();

    var searchParam = {
        p       : DEFAULT_P,
        size    : DEFAULT_SIZE,
    };
    if (p !== 0) searchParam.p = p;

    if (name !== '')        searchParam.name = name;
    if (mobile !== '')      searchParam.mobile = mobile;
    if (email !== '')       searchParam.email = email;

    return searchParam;
}

function renderRoleSelect(roleList, user = false)
{
    // ROOT 用户不允许修改角色
    if (user !== false && user.root == 1) {
        $('#roleSelectBlock').css('display', 'none');
        return;
    }

    var html = '';

    var hadRoleIdList = [];

    if (user !== false) {
        for (var i = 0; i < user.role_list.length; i++) {
            hadRoleIdList.push(user.role_list[i].role_id);
        }
    }

    if (roleList !== false) {
        var list = roleList.list;

        for (var i = 0; i < list.length; i++) {
            html += '<option value="' + list[i].id + '"';
            if (hadRoleIdList.includes(list[i].id)) html += ' selected';
            html += '>';
            html += list[i].name;
            html += '</option>';
        }
    }

    $('#roleSelect').html(html);

    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()
}