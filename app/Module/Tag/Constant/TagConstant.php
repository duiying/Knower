<?php

namespace App\Module\Tag\Constant;

class TagConstant
{
    /**
     * 状态
     * -1：删除；1：正常；
     */
    const TAG_STATUS_DELETE = -1;
    const TAG_STATUS_NORMAL = 1;

    /**
     * 允许的状态
     */
    const ALLOWED_TAG_STATUS_LIST = [
        self::TAG_STATUS_DELETE,
        self::TAG_STATUS_NORMAL,
    ];
}