/**
 * 组装表单参数
 *
 * @returns {{password: (jQuery|string|undefined), name: (jQuery|string|undefined), mobile: (jQuery|string|undefined), position: (jQuery|string|undefined), email: (jQuery|string|undefined)}}
 */
function assembleRoleFormParam(fromUpdate = false)
{
    var name    = $('input[name=name]').val();
    var sort    = $('input[name=sort]').val();

    // 权限 ID 集合
    var permission_id  = '';
    var permissionOptions = $('#rolePermissionSelect option:selected');
    if (permissionOptions.length > 0) {
        for (var i = 0; i < permissionOptions.length; i++) {
            permission_id += permissionOptions[i].value + ',';
        }
    }

    // 菜单 ID 集合
    var menu_id = '';
    var menuInputs = $('#role-menu-check .form-check-input:checked');
    if (menuInputs.length > 0) {
        for (var i = 0; i < menuInputs.length; i++) {
            menu_id += menuInputs[i].value + ',';
        }
    }

    var retFromParam = {
        name : name,
        sort : sort,
    }
    if (permission_id !== '') {
        retFromParam.permission_id = permission_id.substr(0, permission_id.length - 1);
    }
    if (menu_id !== '') {
        retFromParam.menu_id = menu_id.substr(0, menu_id.length - 1);
    }

    if (fromUpdate) {
        retFromParam.id = $('input[name=id]').val();
    }

    return retFromParam;
}

function renderPermissionSelect(permissionList, role = false)
{
    var html = '';

    var hadPermissionIdList = [];

    if (role !== false) {
        for (var i = 0; i < role.permission_list.length; i++) {
            hadPermissionIdList.push(role.permission_list[i].permission_id);
        }
    }

    if (permissionList !== false) {
        var list = permissionList.list;

        for (var i = 0; i < list.length; i++) {
            html += '<option value="' + list[i].id + '"';
            if (hadPermissionIdList.includes(list[i].id)) html += ' selected';
            html += '>';
            html += list[i].name;
            html += '</option>';
        }
    }

    $('#rolePermissionSelect').html(html);

    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()
}

function renderMenuSelect(menuList, role = false)
{
    if (menuList !== false) {
        menuList = menuList.list;

        roleMenuIdList = [];
        if (role !== false && role.menu_list.length > 0) {
            for (var i = 0; i < role.menu_list.length; i++) {
                roleMenuIdList.push(role.menu_list[i].id);
            }
        }

        var menuCheckhtml = '';

        for (var i = 0; i < menuList.length; i++) {
            menuCheckhtml += '<div class="form-check">';
            menuCheckhtml += '<label class="form-check-label"><b>' + menuList[i].name + '</b></label>';
            menuCheckhtml += '</div>';

            for (var j = 0; j < menuList[i].sub_menu_list.length; j++) {
                menuCheckhtml += '<div class="form-check ml-5">';
                if (roleMenuIdList.includes(menuList[i].sub_menu_list[j].id)) {
                    menuCheckhtml += '<input pid="' + menuList[i].id + '" class="form-check-input" type="checkbox" value="' + menuList[i].sub_menu_list[j].id + '" checked>';
                } else {
                    menuCheckhtml += '<input pid="' + menuList[i].id + '" class="form-check-input" type="checkbox" value="' + menuList[i].sub_menu_list[j].id + '">';
                }
                menuCheckhtml += '<label class="form-check-label">' + menuList[i].sub_menu_list[j].name + '</label>';
                menuCheckhtml += '</div>';
            }
        }

        $('#role-menu-check').html(menuCheckhtml);
    }
}