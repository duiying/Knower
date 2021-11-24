function validateTagParam(id)
{
    var rulesInfo = {
        name: {
            required: true,
            minlength: 1,
            maxlength: 50,
        },
        sort: {
            range: [1, 999],
            digits: true,
        }
    };

    var messagesInfo = {
        name: {
            required: "请输入标签名称！",
            minlength: "标签名称过短！",
            maxlength: "标签名称过长！",
        },
        sort: {
            range: '请输入 1-999 之间的整数！',
            digits: '请输入整数！',
        }
    };

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