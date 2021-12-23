<?php

namespace App\Module\ActionLog\Constant;

class ActionLogConstant
{
    // 行为类型 1：浏览文章详情；2：浏览首页；3：发表评论；
    const TYPE_ARTICLE_DETAIL   = 1;
    const TYPE_INDEX            = 2;
    const TYPE_CREATE_COMMENT   = 3;

    // 行为类型文案
    const TYPE_TEXT_MAP = [
        self::TYPE_ARTICLE_DETAIL   => '浏览了文章详情页',
        self::TYPE_INDEX            => '浏览了首页',
        self::TYPE_CREATE_COMMENT   => '发表了评论',
    ];
}