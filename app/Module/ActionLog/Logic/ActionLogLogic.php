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
     * @Inject()
     * @var AccountLogic
     */
    private $accountLogic;

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
        $accountInfoMap = $this->accountLogic->getAccountInfoMapByIdList($accountIdList);

        foreach ($list as $k => $v) {
            $accountId = $v['account_id'];
            $list[$k]['type_text']      = ActionLogConstant::TYPE_TEXT_MAP[$v['type']];
            $list[$k]['account_info']   = $accountId ? $accountInfoMap[$accountId] : new \stdClass();
        }

        $total = $this->service->count($requestData);
        return Util::formatSearchRes($p, $size, $total, $list);
    }
}