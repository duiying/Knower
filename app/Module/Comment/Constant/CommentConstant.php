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

    // 状态 -1：删除；1：正常；
    const COMMENT_STATUS_DELETE = -1;
    const COMMENT_STATUS_NORMAL = 1;

    // 审核状态 -1：违规；0：待审核；1：已审核；
    const AUDIT_ILLEGAL     = -1;
    const AUDIT_WAIT_AUDIT  = 0;
    const AUDIT_AUDITED     = 1;
}