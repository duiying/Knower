<?php

namespace App\Module\Stat\Logic;

use App\Module\Account\Logic\AccountLogic;
use App\Module\ActionLog\Constant\ActionLogConstant;
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
        // 当天游客首页访问量
        $todayTouristIndexCount = 0;
        // 当天登录用户首页访问量
        $todayAccountIndexCount = 0;
        // 本周活跃游客数
        $weekTouristCount = 0;
        // 本周活跃登录用户数
        $weekAccountCount = 0;
        // 本周游客首页访问量
        $weekTouristIndexCount = 0;
        // 本周登录用户首页访问量
        $weekAccountIndexCount = 0;
        // 本月活跃游客数
        $monthTouristCount = 0;
        // 本月活跃登录用户数
        $monthAccountCount = 0;
        // 本月游客首页访问量
        $monthTouristIndexCount = 0;
        // 本月登录用户首页访问量
        $monthAccountIndexCount = 0;
        // 本年活跃游客数
        $yearTouristCount = 0;
        // 本年活跃登录用户数
        $yearAccountCount = 0;
        // 本年游客首页访问量
        $yearTouristIndexCount = 0;
        // 本年登录用户首页访问量
        $yearAccountIndexCount = 0;

        $dayBeginTime   = Util::todayBeginTime();
        $dayEndTime     = Util::todayEndTime();
        $weekBeginTime  = Util::getWeekBeginTime();
        $weekEndTime    = Util::getWeekEndTime();
        $monthBeginTime = Util::getMonthBeginTime();
        $monthEndTime   = Util::getMonthEndTime();
        $yearBeginTime  = Util::getYearBeginTime();
        $yearEndTime    = Util::getYearEndTime();

        $wg = new WaitGroup();
        $wg->add(19);

        Coroutine::create(function () use($wg, &$articleCount) {
            $articleCount = $this->getArticleCount();
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$commentCount) {
            $commentCount = $this->getCommentCount();
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

        Coroutine::create(function () use($wg, &$todayTouristIndexCount, $dayBeginTime, $dayEndTime) {
            $todayTouristIndexCount = $this->getTouristActionLogCount($dayBeginTime, $dayEndTime, ActionLogConstant::TYPE_INDEX);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$weekTouristIndexCount, $weekBeginTime, $weekEndTime) {
            $weekTouristIndexCount = $this->getTouristActionLogCount($weekBeginTime, $weekEndTime, ActionLogConstant::TYPE_INDEX);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$monthTouristIndexCount, $monthBeginTime, $monthEndTime) {
            $monthTouristIndexCount = $this->getTouristActionLogCount($monthBeginTime, $monthEndTime, ActionLogConstant::TYPE_INDEX);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$yearTouristIndexCount, $yearBeginTime, $yearEndTime) {
            $yearTouristIndexCount = $this->getTouristActionLogCount($yearBeginTime, $yearEndTime,  ActionLogConstant::TYPE_INDEX);
            $wg->done();
        });

        Coroutine::create(function () use($wg, &$todayAccountIndexCount, $dayBeginTime, $dayEndTime) {
            $todayAccountIndexCount = $this->getAccountActionLogCount($dayBeginTime, $dayEndTime, ActionLogConstant::TYPE_INDEX);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$weekAccountIndexCount, $weekBeginTime, $weekEndTime) {
            $weekAccountIndexCount = $this->getAccountActionLogCount($weekBeginTime, $weekEndTime, ActionLogConstant::TYPE_INDEX);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$monthAccountIndexCount, $monthBeginTime, $monthEndTime) {
            $monthAccountIndexCount = $this->getAccountActionLogCount($monthBeginTime, $monthEndTime, ActionLogConstant::TYPE_INDEX);
            $wg->done();
        });
        Coroutine::create(function () use($wg, &$yearAccountIndexCount, $yearBeginTime, $yearEndTime) {
            $yearAccountIndexCount = $this->getAccountActionLogCount($yearBeginTime, $yearEndTime,  ActionLogConstant::TYPE_INDEX);
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
            'today_tourist_index_count' => $todayTouristIndexCount,
            'week_tourist_index_count'  => $weekTouristIndexCount,
            'month_tourist_index_count' => $monthTouristIndexCount,
            'year_tourist_index_count'  => $yearTouristIndexCount,
            'today_account_index_count' => $todayAccountIndexCount,
            'week_account_index_count'  => $weekAccountIndexCount,
            'month_account_index_count' => $monthAccountIndexCount,
            'year_account_index_count'  => $yearAccountIndexCount,
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

    /**
     * 获取游客浏览量
     *
     * @param $beginTime
     * @param $endTime
     * @param int $type
     * @return int
     */
    public function getTouristActionLogCount($beginTime, $endTime, $type = 0)
    {
        $actionLogLogic = make(ActionLogLogic::class);
        return $actionLogLogic->getTouristActionLogCount($beginTime, $endTime, $type);
    }

    /**
     * 获取登录用户浏览量
     *
     * @param $beginTime
     * @param $endTime
     * @param int $type
     * @return int
     */
    public function getAccountActionLogCount($beginTime, $endTime, $type = 0)
    {
        $actionLogLogic = make(ActionLogLogic::class);
        return $actionLogLogic->getAccountActionLogCount($beginTime, $endTime, $type);
    }
}