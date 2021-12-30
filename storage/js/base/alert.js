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

function alertSuccess(msg = '操作成功！') {
    Swal.fire({
        position: 'center',
        width: 300,
        toast:true,
        icon: 'success',
        title: msg,
        showConfirmButton: false,
        timer: 1500
    })
}

function alertError(msg = '操作失败！') {
    Swal.fire({
        position: 'center',
        width: 300,
        toast:true,
        icon: 'error',
        title: msg,
        showConfirmButton: false,
        timer: 1500
    })
}

function alertConfirm(callback, msg = '确认操作吗？') {
    Swal.fire({
        position: 'center',
        width: 300,
        title: msg,
        showCancelButton: true,
        confirmButtonText: '确定',
        cancelButtonText: '取消',
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    })
}

async function alertTextarea(callback) {
    const {value: text} = await Swal.fire({
        position: 'center',
        input: 'textarea',
        inputLabel: '备注',
        showCancelButton: true
    })
    if (text !== undefined) {
        callback(text);
    }
}