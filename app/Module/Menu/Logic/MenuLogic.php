<?php

namespace App\Module\Menu\Logic;

use App\RPC\HttpRPC\PassportServiceRpc;
use Hyperf\Di\Annotation\Inject;

class MenuLogic
{
    /**
     * @Inject()
     * @var PassportServiceRpc
     */
    private $passportServiceRpc;

    /**
     * 查找
     *
     * @param $requestData
     * @return mixed
     */
    public function search($requestData)
    {
         return $this->passportServiceRpc->searchMenu($requestData);
    }

    public function create($requestData)
    {
        return $this->passportServiceRpc->createMenu($requestData);
    }

    public function update($requestData)
    {
        return $this->passportServiceRpc->updateMenu($requestData);
    }

    public function updateField($requestData)
    {
        return $this->passportServiceRpc->updateMenuField($requestData);
    }

    public function find($requestData)
    {
        return $this->passportServiceRpc->findMenu($requestData);
    }
}