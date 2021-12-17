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

    // 评论类型文案
    const TYPE_TEXT_MAP = [
        self::TYPE_COMMENT  => '评论',
        self::TYPE_REPLY    => '回复',
        self::TYPE_AT       => '艾特回复',
    ];

    // 状态 -1：已删除；1：正常；
    const COMMENT_STATUS_DELETE = -1;
    const COMMENT_STATUS_NORMAL = 1;

    // 允许的 status 字段
    const ALLOWED_STATUS_LIST = [
        self::COMMENT_STATUS_DELETE,
        self::COMMENT_STATUS_NORMAL,
    ];

    // 状态文案
    const COMMENT_STATUS_TEXT_MAP = [
        self::COMMENT_STATUS_DELETE => '已删除',
        self::COMMENT_STATUS_NORMAL => '正常',
    ];

    // 审核状态 -1：已违规；0：待审核；1：已审核；
    const AUDIT_ILLEGAL     = -1;
    const AUDIT_WAIT_AUDIT  = 0;
    const AUDIT_AUDITED     = 1;

    // 允许的 audit 字段
    const ALLOWED_AUDIT_LIST = [
        self::AUDIT_ILLEGAL,
        self::AUDIT_WAIT_AUDIT,
        self::AUDIT_AUDITED,
    ];

    // 审核状态文案
    const AUDIT_TEXT_MAP = [
        self::AUDIT_ILLEGAL     => '已违规',
        self::AUDIT_WAIT_AUDIT  => '待审核',
        self::AUDIT_AUDITED     => '已审核',
    ];
}