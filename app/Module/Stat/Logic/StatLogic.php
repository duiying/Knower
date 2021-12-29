<?php

namespace App\Module\Stat\Logic;

use App\Module\Account\Logic\AccountLogic;
use App\Module\Article\Logic\ArticleLogic;
use App\Module\Comment\Logic\CommentLogic;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\WaitGroup;

class StatLogic
{
    /**
     * 数据统计
     *
     * @return array
     */
    public function stat()
    {
        // 文章总数量
        $articleCount = 0;
        // 评论总数量
        $commentCount = 0;
        // 用户总数量
        $accountCount = 0;

        $wg = new WaitGroup();
        $wg->add(3);

        Coroutine::create(function () use($wg, &$articleCount) {
            $articleCount = $this->getArticleCount();
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$commentCount) {
            $commentCount = $this->getArticleCount();
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$accountCount) {
            $accountCount = $this->getAccountCount();
            $wg->done();
        });

        $wg->wait();

        return [
            'article_count' => $articleCount,
            'comment_count' => $commentCount,
            'account_count' => $accountCount,
        ];
    }

    /**
     * 文章总数量
     *
     * @return int
     */
    public function getArticleCount()
    {
        $articleLogic = make(ArticleLogic::class);
        return $articleLogic->count();
    }

    /**
     * 评论总数量
     *
     * @return int
     */
    public function getCommentCount()
    {
        $commentLogic = make(CommentLogic::class);
        return $commentLogic->count();
    }

    /**
     * 用户总数量
     *
     * @return int
     */
    public function getAccountCount()
    {
        $accountLogic = make(AccountLogic::class);
        return $accountLogic->count();
    }
}