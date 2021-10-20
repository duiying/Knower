<?php

namespace App\Module\Permission\Logic;

use HyperfPlus\Util\Util;
use Hyperf\Di\Annotation\Inject;
use App\RPC\HttpRPC\PassportServiceRpc;

class PermissionLogic
{
    /**
     * @Inject()
     * @var PassportServiceRpc
     */
    private $passportServiceRpc;

    public function search($requestData)
    {
        return $this->passportServiceRpc->searchPermission($requestData);
    }

    public function create($requestData)
    {
        return $this->passportServiceRpc->createPermission($requestData);
    }

    public function update($requestData)
    {
        return $this->passportServiceRpc->updatePermission($requestData);
    }

    public function updateField($requestData)
    {
        return $this->passportServiceRpc->updatePermissionField($requestData);
    }

    public function find($requestData)
    {
        return $this->passportServiceRpc->findPermission($requestData);
    }
}