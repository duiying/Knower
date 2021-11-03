<?php

namespace App\Module\AdminPassport\Role\Logic;

use App\Constant\AppErrorCode;
use App\Module\AdminPassport\Menu\Logic\MenuLogic;
use App\Module\AdminPassport\Permission\Logic\PermissionLogic;
use App\Module\AdminPassport\Role\Constant\RoleConstant;
use App\Module\AdminPassport\RoleMenu\Constant\RoleMenuConstant;
use App\Module\AdminPassport\RoleMenu\Service\RoleMenuService;
use App\Module\AdminPassport\RolePermission\Constant\RolePermissionConstant;
use App\Module\AdminPassport\RolePermission\Service\RolePermissionService;
use App\Util\Exception\AppException;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use App\Module\AdminPassport\Role\Service\RoleService;

class RoleLogic
{
    /**
     * @Inject()
     * @var RoleService
     */
    private $service;

    /**
     * @Inject()
     * @var RolePermissionService
     */
    private $rolePermissionService;

    /**
     * @Inject()
     * @var RoleMenuService
     */
    private $roleMenuService;

    /**
     * @Inject()
     * @var PermissionLogic
     */
    private $permissionLogic;

    /**
     * @Inject()
     * @var MenuLogic
     */
    private $menuLogic;

    /**
     * 检查角色名称是否重复
     *
     * @param $name
     * @param int $id
     */
    public function checkNameRepeat($name, $id = 0)
    {
        $role = $this->service->getLineByWhere(['name' => $name, 'status' => RoleConstant::ROLE_STATUS_NORMAL]);
        if (empty($role)) return;
        if ($role['id'] != $id) throw new AppException(AppErrorCode::ROLE_NAME_REPEAT_ERROR);
    }

    /**
     * 检查角色是否存在并返回
     *
     * @param $id
     * @return array
     */
    public function checkRole($id)
    {
        $role = $this->service->getLineByWhere(['id' => $id, 'status' => RoleConstant::ROLE_STATUS_NORMAL]);
        if (empty($role)) throw new AppException(AppErrorCode::ROLE_NOT_EXIST_ERROR);
        return $role;
    }

    /**
     * 超级管理员角色不允许更新
     *
     * @param $admin
     */
    public function checkAdmin($admin)
    {
        if ($admin == RoleConstant::ADMIN_YES) throw new AppException(AppErrorCode::ROLE_ADMIN_UPDATE_ERROR);
    }

    /**
     * 检查 status 字段
     *
     * @param $status
     */
    public function checkStatus($status)
    {
        if (!in_array($status, RoleConstant::ALLOWED_ROLE_STATUS_LIST)) {
            throw new AppException(AppErrorCode::PARAMS_INVALID, 'status 参数错误！');
        }
    }

    public function create($requestData)
    {
        // 权限
        $permissionId       = isset($requestData['permission_id']) ? $requestData['permission_id'] : '';
        $permissionIdArr    = Util::ids2IdArr($permissionId);
        if (isset($requestData['permission_id'])) unset($requestData['permission_id']);

        // 菜单
        $menuId             = isset($requestData['menu_id']) ? $requestData['menu_id'] : '';
        $menuIdArr          = Util::ids2IdArr($menuId);
        if (isset($requestData['menu_id'])) unset($requestData['menu_id']);

        // 检查角色名称是否重复
        $this->checkNameRepeat($requestData['name']);

        $this->service->beginTransaction();

        try {
            // 创建角色
            $roleId = $this->service->create($requestData);

            // 创建角色权限
            if (!empty($permissionIdArr)) {
                foreach ($permissionIdArr as $k => $v) {
                    // 检查权限是否已删除
                    $this->permissionLogic->checkPermission($v);

                    // 创建角色权限
                    $this->rolePermissionService->create(['role_id' => $roleId, 'permission_id' => $v]);
                }
            }

            // 创建角色菜单
            if (!empty($menuIdArr)) {
                foreach ($menuIdArr as $k => $v) {
                    // 检查菜单是否已删除
                    $this->menuLogic->checkMenu($v);

                    // 创建角色菜单
                    $this->roleMenuService->create(['role_id' => $roleId, 'menu_id' => $v]);
                }
            }

            $this->service->commit();
        } catch (\Exception $exception) {
            $this->service->rollBack();
            Log::error('创建角色失败', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'requestData' => $requestData]);
            throw new AppException($exception->getCode(), $exception->getMessage());
        }

        return $roleId;
    }

    /**
     * 更新
     *
     * @param $requestData
     * @return int
     */
    public function update($requestData)
    {
        $id = $requestData['id'];
        unset($requestData['id']);

        // 检查角色是否存在
        $role = $this->checkRole($id);
        // 超级管理员角色不允许更新
        $this->checkAdmin($role['admin']);
        // 检查角色名称是否重复
        $this->checkNameRepeat($requestData['name'], $id);

        // 权限
        $permissionId       = isset($requestData['permission_id']) ? $requestData['permission_id'] : '';
        $permissionIdArr    = Util::ids2IdArr($permissionId);
        if (isset($requestData['permission_id'])) unset($requestData['permission_id']);

        // 菜单
        $menuId             = isset($requestData['menu_id']) ? $requestData['menu_id'] : '';
        $menuIdArr          = Util::ids2IdArr($menuId);
        if (isset($requestData['menu_id'])) unset($requestData['menu_id']);

        $this->service->beginTransaction();

        try {
            // 更新角色
            $this->service->update(['id' => $id], $requestData);

            // 将角色的所有权限置为已删除
            $this->rolePermissionService->update(['role_id' => $id], ['status' => RolePermissionConstant::ROLE_PERMISSION_STATUS_DELETE]);

            if (!empty($permissionIdArr)) {
                foreach ($permissionIdArr as $k => $v) {
                    // 检查权限是否已删除
                    $this->permissionLogic->checkPermission($v);

                    if ($this->rolePermissionService->search(['role_id' => $id, 'permission_id' => $v])) {
                        // 恢复角色权限
                        $this->rolePermissionService->update(['role_id' => $id, 'permission_id' => $v], ['status' => RolePermissionConstant::ROLE_PERMISSION_STATUS_NORMAL]);
                    } else {
                        // 创建角色权限
                        $this->rolePermissionService->create(['role_id' => $id, 'permission_id' => $v]);
                    }
                }
            }

            // 将角色的所有菜单置为已删除
            $this->roleMenuService->update(['role_id' => $id], ['status' => RoleMenuConstant::ROLE_MENU_STATUS_DELETE]);
            if (!empty($menuIdArr)) {
                foreach ($menuIdArr as $k => $v) {
                    // 检查菜单是否已删除
                    $this->menuLogic->checkMenu($v);

                    if ($this->roleMenuService->search(['role_id' => $id, 'menu_id' => $v])) {
                        // 恢复角色菜单
                        $this->roleMenuService->update(['role_id' => $id, 'menu_id' => $v], ['status' => RoleMenuConstant::ROLE_MENU_STATUS_NORMAL]);
                    } else {
                        // 创建角色菜单
                        $this->roleMenuService->create(['role_id' => $id, 'menu_id' => $v]);
                    }
                }
            }

            $this->service->commit();
        } catch (\Exception $exception) {
            $this->service->rollBack();
            Log::error('更新角色失败', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'requestData' => $requestData]);
            throw new AppException($exception->getCode(), $exception->getMessage());
        }

        return true;
    }

    /**
     * 更新字段
     *
     * @param $requestData
     * @return int
     */
    public function updateField($requestData)
    {
        $id = $requestData['id'];
        unset($requestData['id']);
        // 检查角色是否存在
        $role = $this->checkRole($id);
        // 超级管理员角色不允许更新
        $this->checkAdmin($role['admin']);
        // 检查角色名称是否重复
        if (isset($requestData['name'])) $this->checkNameRepeat($requestData['name'], $id);
        // 检查 status 字段
        if (isset($requestData['status'])) $this->checkStatus($requestData['status']);

        return $this->service->update(['id' => $id], $requestData);
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
        $requestData['status'] = RoleConstant::ROLE_STATUS_NORMAL;
        $list  = $this->service->search($requestData, $p, $size, ['*'], ['admin' => 'desc', 'sort' => 'asc']);
        $total = $this->service->count($requestData);

        $roleIdList = empty($list) ? [] : array_column($list, 'id');

        // 角色对应的权限
        $permissionListGroupByRoleId = [];
        if (!empty($roleIdList)) {
            $permissionList = $this->rolePermissionService->getRolePermissionByIdList($roleIdList);
            if (!empty($permissionList)) {
                foreach ($permissionList as $k => $v) {
                    $permissionListGroupByRoleId[$v['role_id']][] = $v;
                }
            }
        }

        // 角色对应的菜单
        $menuListGroupByRoleId = [];
        if (!empty($roleIdList)) {
            $menuList = $this->roleMenuService->getRoleMenuByIdList($roleIdList);
            if (!empty($menuList)) {
                foreach ($menuList as $k => $v) {
                    $menuListGroupByRoleId[$v['role_id']][] = $v;
                }
            }
        }

         foreach ($list as $k => $v) {
             $list[$k]['permission_list'] = isset($permissionListGroupByRoleId[$v['id']]) ? $permissionListGroupByRoleId[$v['id']] : [];
             $list[$k]['menu_list'] = isset($menuListGroupByRoleId[$v['id']]) ? $menuListGroupByRoleId[$v['id']] : [];
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
        $id     = $requestData['id'];
        $role   = $this->checkRole($id);

        // 角色对应的权限
        $role['permission_list'] = $this->rolePermissionService->getRolePermissionByIdList([$id]);

        // 角色对应的菜单
        $menuList = $this->roleMenuService->getRoleMenuByIdList([$id]);
        $role['menu_list'] = $menuList;

        return $role;
    }
}