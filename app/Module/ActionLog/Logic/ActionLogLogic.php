<?php

namespace App\Module\ActionLog\Logic;

use App\Constant\AppErrorCode;
use App\Constant\RedisKeyConst;
use App\Module\Account\Logic\AccountLogic;
use App\Module\ActionLog\Constant\ActionLogConstant;
use App\Module\ActionLog\Service\ActionLogService;
use App\Util\AppException;
use App\Util\Log;
use App\Util\Redis;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Coroutine;

class ActionLogLogic
{
    /**
     * @Inject()
     * @var ActionLogService
     */
    private $service;

    /**
     * 创建一条用户行为日志记录
     *
     * @param $accountId
     * @param $thirdId
     * @param $type
     * @param $snapshot
     * @param $ip
     */
    public function create($accountId, $thirdId, $type, $snapshot, $ip)
    {
        Coroutine::create(function () use($accountId, $thirdId, $type, $snapshot, $ip) {
            $addr = $this->ip2region($ip);
            $createParams = [
                'account_id'    => $accountId,
                'third_id'      => $thirdId,
                'type'          => $type,
                'snapshot'      => $snapshot,
                'ip'            => $ip,
                'addr'          => $addr
            ];
            $id = $this->service->create($createParams);

            if (intval(env('QY_WECHAT_SWITCH')) === 1) {
                // 企业微信通知
                $accountStr     = $accountId ? (string)$accountId : '游客';
                $typeText       = ActionLogConstant::TYPE_TEXT_MAP[$type];
                $notifyMsg      = sprintf('日志 ID：%d 用户：%s 地区：%s %s', $id, $accountStr, $addr, $typeText);
                Util::QYWechatNotify($notifyMsg);
            }
        });
    }

    /**
     * 查找
     *
     * @param $requestData
     * @param $p
     * @param $size
     * @return array
     */
    public function search($requestData, $p, $size)
    {
        $list  = $this->service->search($requestData, $p, $size,
            ['*'],
            ['ctime' => 'desc']
        );

        $accountIdList = empty($list) ? [] : array_column($list, 'account_id');
        $accountLogic = make(AccountLogic::class);
        $accountInfoMap = $accountLogic->getAccountInfoMapByIdList($accountIdList);

        foreach ($list as $k => $v) {
            $accountId = $v['account_id'];
            $list[$k]['type_text']      = ActionLogConstant::TYPE_TEXT_MAP[$v['type']];
            $list[$k]['account_info']   = $accountId ? $accountInfoMap[$accountId] : new \stdClass();
        }

        $total = $this->service->count($requestData);
        return Util::formatSearchRes($p, $size, $total, $list);
    }

    /**
     * 获取活跃游客数
     *
     * @param $beginTime
     * @param $endTime
     * @param int $type
     * @return int
     */
    public function getTouristCount($beginTime, $endTime, $type = 0)
    {
        return $this->service->getTouristCount($beginTime, $endTime, $type);
    }

    /**
     * 获取活跃登录用户数
     *
     * @param $beginTime
     * @param $endTime
     * @param int $type
     * @return int
     */
    public function getActiveAccountCount($beginTime, $endTime, $type = 0)
    {
        return $this->service->getActiveAccountCount($beginTime, $endTime, $type);
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
        return $this->service->getAccountActionLogCount($beginTime, $endTime, $type);
    }

    /**
     * 获取游客浏览量
     *
     * @param $beginTime
     * @param $endTime
     * @param int $type
     * @return int
     */
    public function getTouristActionLogCount($beginTime, $endTime, $type)
    {
        return $this->service->getTouristActionLogCount($beginTime, $endTime, $type);
    }

    /**
     * IP 转化为地址
     *
     * @param string $ip
     * @return string
     */
    public function ip2region($ip = '')
    {
        if (empty($ip)) return '';

        $redis          = Redis::instance();
        $redisKey       = RedisKeyConst::IP_REGION . $ip;
        $redisExpire    = 86400 * 2;

        // 1、我先查一下缓存有没有该 IP 的信息
        $formatAddr = $redis->get($redisKey);
        if ($formatAddr) {
            return $formatAddr;
        }

        // 2、我再查一下数据表里有没有该 IP 的信息，查到了就写缓存，减少 MySQL 的 IO 次数
        $actionLogInfo = $this->service->getLineByWhere(['ip' => $ip, 'addr' => ['!=', '']], ['id', 'ip', 'addr'], ['id' => 'desc']);
        if (!empty($actionLogInfo)) {
            $formatAddr = $actionLogInfo['addr'];
            $redis->set($redisKey, $formatAddr, ['nx', 'ex' => $redisExpire]);
            return $formatAddr;
        }

        // 3、缓存里没有，数据表里也没有，那我只能去调用接口查了
        $iQiYiApi = sprintf('http://ip.geo.iqiyi.com/cityjson?format=json&ip=%s', $ip);
        $iQiYiRes = file_get_contents($iQiYiApi);

        if (empty($iQiYiRes)) {
            Log::error('爱奇艺接口返回信息为空', ['ip' => $ip]);
            return '';
        }
        $iQiYiArr = json_decode($iQiYiRes, true);
        if (empty($iQiYiArr) || !isset($iQiYiArr['data']['country'])) {
            Log::error('爱奇艺接口返回信息出错', ['ip' => $ip, 'res' => $iQiYiRes]);
            return '';
        }

        $country    = $iQiYiArr['data']['country'];
        $province   = $iQiYiArr['data']['province'];
        $city       = $iQiYiArr['data']['city'];
        $isp        = $iQiYiArr['data']['isp'];

        $formatAddr = trim(sprintf('%s %s %s %s', $country, $province, $city, $isp));

        if (empty($formatAddr)) return '';

        // 写缓存
        $redis->set($redisKey, $formatAddr, ['nx', 'ex' => $redisExpire]);

        return $formatAddr;
    }
}