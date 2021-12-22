<?php

namespace App\Module\ActionLog\Logic;

use App\Constant\AppErrorCode;
use App\Module\ActionLog\Service\ActionLogService;
use App\Util\AppException;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;

class ActionLogLogic
{
    /**
     * @Inject()
     * @var ActionLogService
     */
    private $service;

    public function create($accountId, $thirdId, $type, $snapshot, $ip)
    {
        $createParams = [
            'account_id'    => $accountId,
            'third_id'      => $thirdId,
            'type'          => $type,
            'snapshot'      => $snapshot,
            'ip'            => $ip
        ];
        return $this->service->create($createParams);
    }
}