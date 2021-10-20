function validatePermissionParam(id)
{
    $('#' + id).validate({
        rules: {
            name: {
                required: true,
                maxlength: 50,
            },
            url: {
                required: true,
                maxlength: 2000,
            },
            sort: {
                range: [1, 999],
                digits: true,
            },
        },
        messages: {
            name: {
                required: '请输入名称！',
                maxlength: '名称输入过长！',
            },
            url: {
                required: '请输入路由！',
                maxlength: '路由输入过长！',
            },
            sort: {
                range: '请输入 1-999 之间的整数！',
                digits: '请输入整数！',
            },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {handleErrorPlacement(error, element)},
        highlight: function (element, errorClass, validClass) {handleHighlight(element, errorClass, validClass)},
        unhighlight: function (element, errorClass, validClass) {handleUnHighlight(element, errorClass, validClass)},
    });

    return $('#' + id).valid();
}