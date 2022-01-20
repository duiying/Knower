const BASE_API_URL = 'http://127.0.0.1:9501/';

const REDIRECT_LOGIN_ERROR_CODE = [3000510, 3000511, 3000512, 3000513];

request = {
    post : function (url, data = {}, fromBackend = true) {
        var res = false;

        // 后台调用 API 需要验证 token
        if (fromBackend) {
            var token = checkToken(url);
            if (token !== '') data.access_token = token;
        }

        $.ajax({
            type 		: 'POST',
            url         : BASE_API_URL + url,
            data        : data,
            dataType    : 'json',
            async       : false,
            success     : function(resp) {
                if (resp.code === 0) {
                    res = resp.data;
                }

                if (fromBackend) {
                    if (resp.code !== 0) {
                        if (resp.msg !== '') {
                            alert.error(resp.msg);
                        } else {
                            alert.error('操作失败，请稍后重试，如果失败多次请联系技术解决！');
                        }
                        console.log(resp);
                    } else {
                        if (resp.msg !== '') {
                            alert.success(resp.msg);
                        } else {
                            alert.success('操作成功！');
                        }
                    }
                } else {
                    if (resp.code !== 0) {
                        if (resp.msg !== '') {
                            alertError(resp.msg);
                            if (REDIRECT_LOGIN_ERROR_CODE.indexOf(resp.code)) {
                                if ($.cookie('knower_access_token')) {
                                    $.cookie('knower_access_token', '', {expires: -1});
                                }
                                setTimeout(function () {
                                    location.href = '/login'
                                }, 1000);
                            }
                        } else {
                            alertError('操作失败，请稍后重试，如果失败多次请联系技术解决！');
                            if (REDIRECT_LOGIN_ERROR_CODE.indexOf(resp.code)) {
                                if ($.cookie('knower_access_token')) {
                                    $.cookie('knower_access_token', '', {expires: -1});
                                }
                                setTimeout(function () {
                                    location.href = '/login'
                                }, 1000);
                            }
                        }
                    } else {
                        if (resp.msg !== '') {
                            alertSuccess(resp.msg);
                        } else {
                            alertSuccess('操作成功！');
                        }
                    }
                }
            }
        });

        return res;
    },
    get : function (url, data = {}, fromBackend = true) {
        var res = false;

        // 后台调用 API 需要验证 token
        if (fromBackend) {
            var token = checkToken(url);
            if (token !== '') data.access_token = token;
        }

        $.ajax({
            type 		: 'GET',
            url         : BASE_API_URL + url,
            data        : data,
            dataType    : 'json',
            async       : false,
            success     : function(resp) {
                if (resp.code === 0) {
                    res = resp.data;
                }

                if (fromBackend) {
                    if (resp.code !== 0) {
                        if (resp.msg !== '') {
                            alert.error(resp.msg);
                        } else {
                            alert.error('操作失败，请稍后重试，如果失败多次请联系技术解决！');
                        }
                        console.log(resp);
                    }
                } else {
                    if (resp.code !== 0) {
                        if (resp.msg !== '') {
                            alertError(resp.msg);
                            if (REDIRECT_LOGIN_ERROR_CODE.indexOf(resp.code)) {
                                if ($.cookie('knower_access_token')) {
                                    $.cookie('knower_access_token', '', {expires: -1});
                                }
                                setTimeout(function () {
                                    location.href = '/login'
                                }, 1000);
                            }
                        } else {
                            alertError('操作失败，请稍后重试，如果失败多次请联系技术解决！');
                            if (REDIRECT_LOGIN_ERROR_CODE.indexOf(resp.code)) {
                                if ($.cookie('knower_access_token')) {
                                    $.cookie('knower_access_token', '', {expires: -1});
                                }
                                setTimeout(function () {
                                    location.href = '/login'
                                }, 1000);
                            }
                        }
                    }
                }
            }
        });

        return res;
    },
}

function checkToken(url)
{
    // 不需要检查 token 的路由
    var noCheckUrl = ['v1/user/login', 'view/user/login'];
    if (noCheckUrl.includes(url)) {
        return '';
    }

    // 开始检查 token
    var token = $.cookie('access_token');
    if (!token) {
        // 跳转到登录页
        location.href = '/view/user/login';
    }

    return token;
}
