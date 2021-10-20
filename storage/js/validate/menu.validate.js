function validateMenuParam(id)
{
    $('#' + id).validate({
        rules: {
            pid: {
                required: true,
                min: 0,
                digits: true,
            },
            name: {
                required: true,
                maxlength: 20,
            },
            icon: {
                required: true,
                maxlength: 50,
            },
            sort: {
                range: [1, 999],
                digits: true,
            },
        },
        messages: {
            pid: {
                required: '请选择上级！',
                min: '最小为 0！',
                digits: '必须为数字',
            },
            name: {
                required: '请输入标题！',
                maxlength: '标题输入过长！',
            },
            icon: {
                required: '请输入图标！',
                maxlength: '图标输入过长！',
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