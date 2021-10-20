alert = {
    success : function (msg = '操作成功！') {
        $('#toast-container').html('');
        toastr.success(msg);
    },
    error : function (msg = '操作失败！') {
        $('#toast-container').html('');
        toastr.error(msg);
    },
}