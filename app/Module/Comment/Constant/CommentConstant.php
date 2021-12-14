<?php

namespace App\Module\Comment\Constant;

class CommentConstant
{
    // 第三方类型 1：文章；
    const THIRD_TYPE_ARTICLE = 1;

    // 类型 1：普通评论；2：回复；3：艾特回复；
    const TYPE_COMMENT  = 1;
    const TYPE_REPLY    = 2;
    const TYPE_AT       = 3;
}