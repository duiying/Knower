<?php

namespace App\Module\AdminPassport\Permission\Logic;

use App\Constant\AppErrorCode;
use App\Module\AdminPassport\Permission\Constant\PermissionConstant;
use App\Util\Exception\AppException;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use App\Module\AdminPassport\Permission\Service\PermissionService;

class PermissionLogic
{
    /**
     * @Inject()
     * @var PermissionService
     */
    private $service;

    /**
     * 检查权限是否存在并返回
     *
     * @param $permissionId
     * @return array
     */
    public function checkPermission($permissionId)
    {
        $permission = $this->service->getLineByWhere(['id' => $permissionId, 'status' => PermissionConstant::PERMISSION_STATUS_NORMAL]);
        if (empty($permission)) throw new AppException(AppErrorCode::PERMISSION_NOT_EXIST_ERROR);
        return $permission;
    }

    /**
     * 检查 status 字段
     *
     * @param $status
     */
    public function checkStatus($status)
    {
        if (!in_array($status, PermissionConstant::ALLOWED_PERMISSION_STATUS_LIST)) {
            throw new AppException(AppErrorCode::REQUEST_PARAMS_INVALID, 'status 参数错误！');
        }
    }

    /**
     * 创建
     *
     * @param $requestData
     * @return int
     */
    public function create($requestData)
    {
        $requestData['url'] = rtrim($requestData['url'], ';');
        return $this->service->create($requestData);
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

        // 检查 status 字段
        if (isset($requestData['status'])) {
            $this->checkStatus($requestData['status']);
        }

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
        $requestData['status'] = PermissionConstant::PERMISSION_STATUS_NORMAL;
        $list  = $this->service->search($requestData, $p, $size, ['*'], ['sort' => 'asc', 'id' => 'desc']);
        $total = $this->service->count($requestData);
        foreach ($list as $k => $v) {
            $list[$k]['url_list'] = !empty($v['url']) ? explode(';', $v['url']) : [];
        }
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