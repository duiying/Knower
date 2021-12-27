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

