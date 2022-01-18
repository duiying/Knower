<?php

namespace App\Module\ActionLog\Logic;

use App\Constant\AppErrorCode;
use App\Module\Account\Logic\AccountLogic;
use App\Module\ActionLog\Constant\ActionLogConstant;
use App\Module\ActionLog\Service\ActionLogService;
use App\Util\AppException;
use App\Util\Log;
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
            $createParams = [
                'account_id'    => $accountId,
                'third_id'      => $thirdId,
                'type'          => $type,
                'snapshot'      => $snapshot,
                'ip'            => $ip
            ];
            $this->service->create($createParams);
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
}