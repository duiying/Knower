<?php

namespace App\Module\AdminPassport\Menu\Constant;

class MenuConstant
{
    /**
     * 菜单状态
     */
    const MENU_STATUS_DELETE = -1;      // 删除
    const MENU_STATUS_NORMAL = 1;       // 正常

    /**
     * 允许的菜单状态
     */
    const ALLOWED_MENU_STATUS_LIST = [
        self::MENU_STATUS_DELETE,
        self::MENU_STATUS_NORMAL,
    ];
}