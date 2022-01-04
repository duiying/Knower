/******************** 管理员 API begin *********************************************************************************/

function searchUser(data = {}) {
    return request.get('v1/user/search', data);
}

function findUser(data = {}) {
    return request.get('v1/user/find', data);
}

function getUserInfo(data = {}) {
    return request.get('v1/user/get_info', data);
}

function getUserMenu(data = {}) {
    return request.get('v1/user/menu', data);
}

function createUser(data = {}) {
    return request.post('v1/user/create', data);
}

function updateUser(data = {}) {
    return request.post('v1/user/update', data);
}

function deleteUser(data = {}) {
    return request.post('v1/user/delete', data);
}

function userLogin(data = {}) {
    return request.post('v1/user/login', data);
}

function userLogout(data = {}) {
    return request.post('v1/user/logout', data);
}

/******************** 管理员 API end ***********************************************************************************/

/******************** 菜单 API begin ***********************************************************************************/

function searchMenu(data = {}) {
    return request.get('v1/menu/search', data);
}

function selectMenu(data = {}) {
    return request.get('v1/menu/select', data);
}

function createMenu(data = {}) {
    return request.post('v1/menu/create', data);
}

function updateMenu(data = {}) {
    return request.post('v1/menu/update', data);
}

function findMenu(data = {}) {
    return request.get('v1/menu/find', data);
}

function deleteMenu(data = {}) {
    return request.post('v1/menu/delete', data);
}

/******************** 菜单 API end *************************************************************************************/

/******************** 权限 API begin ***********************************************************************************/

function searchPermission(data = {}) {
    return request.get('v1/permission/search', data);
}

function selectPermission(data = {}) {
    return request.get('v1/permission/select', data);
}

function createPermission(data = {}) {
    return request.post('v1/permission/create', data);
}

function updatePermission(data = {}) {
    return request.post('v1/permission/update', data);
}

function findPermission(data = {}) {
    return request.get('v1/permission/find', data);
}

function deletePermission(data = {}) {
    return request.post('v1/permission/delete', data);
}

/******************** 权限 API end *************************************************************************************/

/******************** 角色 API begin ***********************************************************************************/

function searchRole(data = {}) {
    return request.get('v1/role/search', data);
}

function selectRole(data = {}) {
    return request.get('v1/role/select', data);
}

function createRole(data = {}) {
    return request.post('v1/role/create', data);
}

function updateRole(data = {}) {
    return request.post('v1/role/update', data);
}

function findRole(data = {}) {
    return request.get('v1/role/find', data);
}

function deleteRole(data = {}) {
    return request.post('v1/role/delete', data);
}

/******************** 角色 API end *************************************************************************************/

/******************** 文章 API begin ***********************************************************************************/

function searchArticle(data = {}) {
    return request.get('v1/article/search', data);
}

function createArticle(data = {}) {
    return request.post('v1/article/create', data);
}

function updateArticle(data = {}) {
    return request.post('v1/article/update', data);
}

function findArticle(data = {}) {
    return request.get('v1/article/find', data);
}

function detailArticle(data = {}) {
    return request.get('article/info', data, false);
}

function deleteArticle(data = {}) {
    return request.post('v1/article/delete', data);
}

/******************** 文章 API end *************************************************************************************/

/******************** 标签 API begin ***********************************************************************************/

function searchTag(data = {}) {
    return request.get('v1/tag/search', data);
}

function selectTag(data = {}) {
    return request.get('v1/tag/select', data);
}

function listTag(data = {}, checkToken = true) {
    return request.get('tags', data, checkToken);
}

function listArticle(data = {}) {
    return request.get('articles', data, false);
}

function createTag(data = {}) {
    return request.post('v1/tag/create', data);
}

function updateTag(data = {}) {
    return request.post('v1/tag/update', data);
}

function findTag(data = {}) {
    return request.get('v1/tag/find', data);
}

function deleteTag(data = {}) {
    return request.post('v1/tag/delete', data);
}

/******************** 标签 API end *************************************************************************************/

/******************** 用户 API begin ***********************************************************************************/

function getInfoByToken(data = {}) {
    return request.get('account/get_info_by_token', data, false);
}

function searchAccount(data = {}) {
    return request.get('v1/account/search', data);
}

function updateAccountStatus(data = {}) {
    return request.post('v1/account/update_status', data);
}

function updateAccountMarkField(data = {}) {
    return request.post('v1/account/update_mark_field', data);
}

/******************** 用户 API end *************************************************************************************/

/******************** 评论 API begin ***********************************************************************************/

function comments(data = {}) {
    return request.get('comments', data, false);
}

function createComment(data = {}) {
    return request.post('comment/create', data, false);
}

function deleteComment(data = {}) {
    return request.post('comment/delete', data, false);
}

function searchComment(data = {}) {
    return request.get('v1/comment/search', data);
}

function updateCommentStatus(data = {}) {
    return request.post('v1/comment/update_status', data);
}

/******************** 评论 API end *************************************************************************************/

/******************** 行为日志 API begin ********************************************************************************/

function searchActionLog(data = {}) {
    return request.get('v1/action_log/search', data);
}

/******************** 行为日志 API end **********************************************************************************/

/******************** 数据统计 API begin ********************************************************************************/

function statData(data = {}) {
    return request.get('v1/data/stat', data);
}

/******************** 数据统计 API end **********************************************************************************/