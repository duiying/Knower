<?php

namespace App\Module\AdminPassport\Permission\Constant;

class PermissionConstant
{
    /**
     * 状态
     */
    const PERMISSION_STATUS_DELETE = -1;        // 删除
    const PERMISSION_STATUS_NORMAL = 1;         // 正常

    /**
     * 允许的状态
     */
    const ALLOWED_PERMISSION_STATUS_LIST = [
        self::PERMISSION_STATUS_DELETE,
        self::PERMISSION_STATUS_NORMAL,
    ];
}