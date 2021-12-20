<?php

namespace App\Module\Img\Constant;

class ImgConstant
{
    /**
     * 状态
     * -1：删除；1：正常；
     */
    const IMG_STATUS_DELETE = -1;
    const IMG_STATUS_NORMAL = 1;

    /**
     * 允许的状态
     */
    const ALLOWED_IMGSTATUS_LIST = [
        self::IMG_STATUS_DELETE,
        self::IMG_STATUS_NORMAL,
    ];
}