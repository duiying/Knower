<?php

namespace App\Module\AdminPassport\Role\Constant;

class RoleConstant
{
    /**
     * 状态
     */
    const ROLE_STATUS_DELETE = -1;              // 删除
    const ROLE_STATUS_NORMAL = 1;               // 正常

    /**
     * 允许的状态列表
     */
    const ALLOWED_ROLE_STATUS_LIST = [
        self::ROLE_STATUS_DELETE,
        self::ROLE_STATUS_NORMAL,
    ];

    /**
     * 超级管理员
     */
    const ADMIN_YES     = 1;                    // 是
    const ADMIN_NO      = 0;                    // 否
}