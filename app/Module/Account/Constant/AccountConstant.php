<?php

namespace App\Module\Account\Constant;

class AccountConstant
{
    // 状态 -1：禁用；1：正常；
    const ACCOUNT_STATUS_FORBIDDEN  = -1;
    const ACCOUNT_STATUS_NORMAL     = 1;

    const ACCOUNT_STATUS_TEXT_MAP = [
        self::ACCOUNT_STATUS_FORBIDDEN      => '已禁用',
        self::ACCOUNT_STATUS_NORMAL         => '正常',
    ];

    // 允许的 status 值
    const ALLOWED_STATUS_LIST = [
        self::ACCOUNT_STATUS_FORBIDDEN,
        self::ACCOUNT_STATUS_NORMAL,
    ];
}