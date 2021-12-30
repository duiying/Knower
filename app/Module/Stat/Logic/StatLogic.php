<?php

namespace App\Module\Stat\Logic;

use App\Module\Account\Logic\AccountLogic;
use App\Module\ActionLog\Logic\ActionLogLogic;
use App\Module\Article\Logic\ArticleLogic;
use App\Module\Comment\Logic\CommentLogic;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\WaitGroup;

class StatLogic
{

    public function stat($requestData)
    {
        // 文章总数量
        $articleCount = 0;
        // 评论总数量
        $commentCount = 0;
        // 用户总数量
        $accountCount = 0;
        // 当天活跃游客数
        $todayTouristCount = 0;
        // 当天活跃登录用户数
        $todayAccountCount = 0;
        // 本周活跃游客数
        $weekTouristCount = 0;
        // 本周活跃登录用户数
        $weekAccountCount = 0;
        // 本月活跃游客数
        $monthTouristCount = 0;
        // 本月活跃登录用户数
        $monthAccountCount = 0;
        // 本年活跃游客数
        $yearTouristCount = 0;
        // 本年活跃登录用户数
        $yearAccountCount = 0;

        $dayBeginTime   = Util::todayBeginTime();
        $dayEndTime     = Util::todayEndTime();
        $weekBeginTime  = Util::getWeekBeginTime();
        $weekEndTime    = Util::getWeekEndTime();
        $monthBeginTime = Util::getMonthBeginTime();
        $monthEndTime   = Util::getMonthEndTime();
        $yearBeginTime  = Util::getYearBeginTime();
        $yearEndTime    = Util::getYearEndTime();

        $wg = new WaitGroup();
        $wg->add(11);

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
        Coroutine::create(function () use($wg, &$todayTouristCount, $dayBeginTime, $dayEndTime) {
            $todayTouristCount = $this->getTouristCount($dayBeginTime, $dayEndTime);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$weekTouristCount, $weekBeginTime, $weekEndTime) {
            $weekTouristCount = $this->getTouristCount($weekBeginTime, $weekEndTime);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$monthTouristCount, $monthBeginTime, $monthEndTime) {
            $monthTouristCount = $this->getTouristCount($monthBeginTime, $monthEndTime);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$yearTouristCount, $yearBeginTime, $yearEndTime) {
            $yearTouristCount = $this->getTouristCount($yearBeginTime, $yearEndTime);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$todayAccountCount, $dayBeginTime, $dayEndTime) {
            $todayAccountCount = $this->getActiveAccountCount($dayBeginTime, $dayEndTime);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$weekAccountCount, $weekBeginTime, $weekEndTime) {
            $weekAccountCount = $this->getActiveAccountCount($weekBeginTime, $weekEndTime);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$monthAccountCount, $monthBeginTime, $monthEndTime) {
            $monthAccountCount = $this->getActiveAccountCount($monthBeginTime, $monthEndTime);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$yearAccountCount, $yearBeginTime, $yearEndTime) {
            $yearAccountCount = $this->getActiveAccountCount($yearBeginTime, $yearEndTime);
            $wg->done();
        });

        $wg->wait();

        return [
            'article_count'             => $articleCount,
            'comment_count'             => $commentCount,
            'account_count'             => $accountCount,
            'today_tourist_count'       => $todayTouristCount,
            'week_tourist_count'        => $weekTouristCount,
            'month_tourist_count'       => $monthTouristCount,
            'year_tourist_count'        => $yearTouristCount,
            'today_account_count'       => $todayAccountCount,
            'week_account_count'        => $weekAccountCount,
            'month_account_count'       => $monthAccountCount,
            'year_account_count'        => $yearAccountCount,
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

    /**
     * 获取活跃游客数
     *
     * @param $beginTime
     * @param $endTime
     * @return int
     */
    public function getTouristCount($beginTime, $endTime)
    {
        $actionLogLogic = make(ActionLogLogic::class);
        return $actionLogLogic->getTouristCount($beginTime, $endTime);
    }

    /**
     * 获取活跃登录用户数
     *
     * @param $beginTime
     * @param $endTime
     * @return int
     */
    public function getActiveAccountCount($beginTime, $endTime)
    {
        $actionLogLogic = make(ActionLogLogic::class);
        return $actionLogLogic->getActiveAccountCount($beginTime, $endTime);
    }
}