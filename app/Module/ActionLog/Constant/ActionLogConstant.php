<?php

namespace App\Module\ActionLog\Constant;

class ActionLogConstant
{
    // 行为类型 1：浏览文章详情；2：浏览首页；3：发表评论；4：删除评论；5：QQ 登录；6：GitHub 登录；7：QQ 注册；8：GitHub 注册；
    const TYPE_ARTICLE_DETAIL   = 1;
    const TYPE_INDEX            = 2;
    const TYPE_CREATE_COMMENT   = 3;
    const TYPE_DELETE_COMMENT   = 4;
    const TYPE_QQ_LOGIN         = 5;
    const TYPE_GITHUB_LOGIN     = 6;
    const TYPE_QQ_REGISTER      = 7;
    const TYPE_GITHUB_REGISTER  = 8;

    // 行为类型文案
    const TYPE_TEXT_MAP = [
        self::TYPE_ARTICLE_DETAIL   => '浏览了文章详情页',
        self::TYPE_INDEX            => '浏览了首页',
        self::TYPE_CREATE_COMMENT   => '发表了评论',
        self::TYPE_DELETE_COMMENT   => '删除了评论',
        self::TYPE_QQ_LOGIN         => '通过 QQ 登录了平台',
        self::TYPE_GITHUB_LOGIN     => '通过 GitHub 登录了平台',
        self::TYPE_QQ_REGISTER      => '通过 QQ 注册了平台',
        self::TYPE_GITHUB_REGISTER  => '通过 GitHub 注册了平台',
    ];
}