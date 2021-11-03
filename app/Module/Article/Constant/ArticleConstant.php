<?php

namespace App\Module\Article\Constant;

class ArticleConstant
{
    /**
     * 状态
     */
    const ARTICLE_STATUS_DELETE = -1;               // 删除
    const ARTICLE_STATUS_NORMAL = 1;                // 正常

    /**
     * 允许的状态
     */
    const ALLOWED_ARTICLE_STATUS_LIST = [
        self::ARTICLE_STATUS_DELETE,
        self::ARTICLE_STATUS_NORMAL,
    ];

    /**
     * 操作类型
     */
    const ACTION_TYPE_CREATE = 'action_type_create';
    const ACTION_TYPE_UPDATE = 'action_type_update';
    const ACTION_TYPE_DELETE = 'action_type_delete';
}