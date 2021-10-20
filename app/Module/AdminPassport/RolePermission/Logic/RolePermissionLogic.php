<?php

namespace App\Module\AdminPassport\RolePermission\Logic;

use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use App\Module\AdminPassport\RolePermission\Service\RolePermissionService;

class RolePermissionLogic
{
    /**
     * @Inject()
     * @var RolePermissionService
     */
    private $service;

    /**
     * 创建
     *
     * @param $requestData
     * @return int
     */
    public function create($requestData)
    {
        $data = $requestData;
        return $this->service->create($data);
    }

    /**
     * 更新
     *
     * @param $requestData
     * @return int
     */
    public function update($requestData)
    {
        $data   = $requestData;
        $id     = $requestData['id'];
        unset($data['id']);
        return $this->service->update(['id' => $id], $data);
    }

    /**
     * 更新字段
     *
     * @param $requestData
     * @return int
     */
    public function updateField($requestData)
    {
        $data   = $requestData;
        $id     = $requestData['id'];
        unset($data['id']);
        return $this->service->update(['id' => $id], $data);
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
         $list  = $this->service->search($requestData, $p, $size);
         $total = $this->service->count($requestData);
         return Util::formatSearchRes($p, $size, $total, $list);
    }

    /**
     * 获取一行
     *
     * @param $requestData
     * @return array
     */
    public function find($requestData)
    {
        return $this->service->getLineByWhere($requestData);
    }
}