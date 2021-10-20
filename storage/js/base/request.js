//const BASE_API_URL = 'http://127.0.0.1:9501/';
const BASE_API_URL = 'http://api.micro.service.com/'

request = {
    post : function (url, data = {}) {
        var res = false;

        var token = checkToken(url);
        if (token !== '') data.access_token = token;

        $.ajax({
            type 		: 'POST',
            url         : BASE_API_URL + url,
            data        : data,
            dataType    : 'json',
            async       : false,
            success     : function(resp) {
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
                    res = resp.data;
                }
            }
        });

        return res;
    },
    get : function (url, data = {}) {
        var res = false;

        var token = checkToken(url);
        if (token !== '') data.access_token = token;

        $.ajax({
            type 		: 'GET',
            url         : BASE_API_URL + url,
            data        : data,
            dataType    : 'json',
            async       : false,
            success     : function(resp) {
                if (resp.code !== 0) {
                    if (resp.msg !== '') {
                        alert.error(resp.msg);
                    } else {
                        alert.error('操作失败，请稍后重试，如果失败多次请联系技术解决！');
                    }
                    console.log(resp);
                } else {
                    res = resp.data;
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
