function validateArticleParam(id)
{
    $('#' + id).validate({
        rules: {
            title: {
                required: true,
                maxlength: 255,
            },
            sort: {
                range: [1, 999],
                digits: true,
            },
        },
        messages: {
            name: {
                required: '请输入标题！',
                maxlength: '标题输入过长！',
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