function validateParam(id)
{
    var rulesInfo = {
        name: {
            required: true,
            minlength: 2,
            maxlength: 50,
        },
        mobile: {
            required: true,
            minlength: 11,
            maxlength: 11,
        },
        email: {
            required: true,
            email: true,
            maxlength: 50,
        },
        position: {
            required: true,
            maxlength: 50,
        }
    };

    var messagesInfo = {
        name: {
            required: "请输入真实姓名！",
            minlength: "真实姓名过短！",
            maxlength: "真实姓名输入过长！",
        },
        mobile: {
            required: "请输入手机号！",
            minlength: "手机号为 11 位！",
            maxlength: "手机号为 11 位！",
        },
        email: {
            required: "请输入公司邮箱！",
            email: "请输入有效的邮箱地址！",
            maxlength: "邮箱地址输入过长！",
        },
        position: {
            required: "请输入职位！",
            maxlength: "职位输入过长！",
        }
    };

    if (id === 'user-create') {
        rulesInfo.password = {
            required: true,
            minlength: 6,
            maxlength: 32,
        };
        rulesInfo.rePassword = {
            required: true,
            minlength: 6,
            maxlength: 32,
            equalTo: 'input[name=password]',
        };
        messagesInfo.password = {
            required: "请输入密码！",
            minlength: "密码最少为 6 位！",
            maxlength: "密码输入过长！",
        };
        messagesInfo.rePassword = {
            required: "请输入确认密码！",
            minlength: "确认密码最少为 6 位！",
            maxlength: "重复密码输入过长！",
            equalTo: "两次密码输入不一致！",
        };
    }

    $('#' + id).validate({
        rules : rulesInfo,
        messages : messagesInfo,
        errorElement: 'span',
        errorPlacement: function (error, element) {handleErrorPlacement(error, element)},
        highlight: function (element, errorClass, validClass) {handleHighlight(element, errorClass, validClass)},
        unhighlight: function (element, errorClass, validClass) {handleUnHighlight(element, errorClass, validClass)},
    });

    return $('#' + id).valid();
}