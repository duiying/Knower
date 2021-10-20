<?php

namespace App\Module\Role\Logic;

use App\RPC\HttpRPC\PassportServiceRpc;
use Hyperf\Di\Annotation\Inject;

class RoleLogic
{
    /**
     * @Inject()
     * @var PassportServiceRpc
     */
    private $passportServiceRpc;

    public function search($requestData)
    {
        return $this->passportServiceRpc->searchRole($requestData);
    }

    public function create($requestData)
    {
        return $this->passportServiceRpc->createRole($requestData);
    }

    public function update($requestData)
    {
        return $this->passportServiceRpc->updateRole($requestData);
    }

    public function updateField($requestData)
    {
        return $this->passportServiceRpc->updateRoleField($requestData);
    }

    public function find($requestData)
    {
        return $this->passportServiceRpc->findRole($requestData);
    }
}