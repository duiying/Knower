<?php

namespace App\Module\AdminPassport\User\Logic;

use App\Constant\AppErrorCode;
use App\Constant\CommonConstant;
use App\Module\AdminPassport\Menu\Constant\MenuConstant;
use App\Module\AdminPassport\Menu\Logic\MenuLogic;
use App\Module\AdminPassport\Menu\Service\MenuService;
use App\Module\AdminPassport\Role\Constant\RoleConstant;
use App\Module\AdminPassport\Role\Logic\RoleLogic;
use App\Module\AdminPassport\RoleMenu\Service\RoleMenuService;
use App\Module\AdminPassport\RolePermission\Service\RolePermissionService;
use App\Module\AdminPassport\User\Constant\UserConstant;
use App\Module\AdminPassport\UserRole\Constant\UserRoleConstant;
use App\Module\AdminPassport\UserRole\Service\UserRoleService;
use App\Util\Exception\AppException;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use App\Module\AdminPassport\User\Service\UserService;

class UserLogic
{
    /**
     * @Inject()
     * @var UserService
     */
    private $service;

    /**
     * @Inject()
     * @var MenuService
     */
    private $menuService;

    /**
     * @Inject()
     * @var UserRoleService
     */
    private $userRoleService;

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
     * @var RoleLogic
     */
    private $roleLogic;

    /**
     * @Inject()
     * @var MenuLogic
     */
    private $menuLogic;

    public static function encryptPassword($password)
    {
        return md5('passport_&_#' . $password);
    }

    public static function generateToken($userId)
    {
        return md5(uniqid(mt_rand(), true) . $userId . mt_rand());
    }

    /**
     * 检查管理员邮箱是否重复
     *
     * @param $email
     * @param int $id
     */
    public function checkEmailRepeat($email, $id = 0)
    {
        $user = $this->service->getLineByWhere(['email' => $email, 'status' => UserConstant::USER_STATUS_NORMAL]);
        if (empty($user)) return;
        if ($user['id'] != $id) throw new AppException(AppErrorCode::EMAIL_REPEAT_ERROR);
    }

    /**
     * 检查角色是否存在并返回
     *
     * @param $id
     * @return array
     */
    public function checkUser($id)
    {
        $user = $this->service->getLineByWhere(['id' => $id, 'status' => UserConstant::USER_STATUS_NORMAL]);
        if (empty($user)) throw new AppException(AppErrorCode::USER_NOT_EXIST_ERROR);
        return $user;
    }

    /**
     * 检查 status 字段
     *
     * @param $status
     */
    public function checkUserStatus($status)
    {
        if (!in_array($status, UserConstant::ALLOWED_USER_STATUS_LIST)) {
            throw new AppException(AppErrorCode::PARAMS_INVALID, 'status 参数错误！');
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
        $roleId         = isset($requestData['role_id']) ? $requestData['role_id'] : '';
        $roleIdArr      = Util::ids2IdArr($roleId);
        if (isset($requestData['role_id'])) unset($requestData['role_id']);
        $requestData['password'] = self::encryptPassword($requestData['password']);

        // 检查邮箱是否重复
        $this->checkEmailRepeat($requestData['email']);

        $this->service->beginTransaction();

        try {
            // 创建用户
            $userId = $this->service->create($requestData);

            // 创建用户角色
            if (!empty($roleIdArr)) {
                foreach ($roleIdArr as $k => $v) {
                    // 检查角色
                    $this->roleLogic->checkRole($v);

                    // 创建角色权限
                    $this->userRoleService->create(['user_id' => $userId, 'role_id' => $v]);
                }
            }

            $this->service->commit();
        } catch (\Exception $exception) {
            $this->service->rollBack();
            Log::error('创建管理员失败', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'requestData' => $requestData]);
            throw new AppException($exception->getCode(), $exception->getMessage());
        }

        return $userId;
    }

    /**
     * 更新
     *
     * @param $requestData
     * @return int
     */
    public function update($requestData)
    {
        $id     = $requestData['id'];
        unset($requestData['id']);
        if (isset($requestData['password'])) $requestData['password'] = self::encryptPassword($requestData['password']);

        // 检查用户是否存在
        $user = $this->checkUser($id);
        // ROOT 用户不允许更新角色
        if ($user['root'] == UserConstant::IS_ROOT && isset($requestData['role_id'])) {
            unset($requestData['role_id']);
        }
        // 检查邮箱是否重复
        $this->checkEmailRepeat($requestData['email'], $user['id']);

        $roleId         = isset($requestData['role_id']) ? $requestData['role_id'] : '';
        $roleIdArr      = Util::ids2IdArr($roleId);
        if (isset($requestData['role_id'])) unset($requestData['role_id']);

        $this->service->beginTransaction();

        try {
            // 更新管理员
            $this->service->update(['id' => $id], $requestData);

            if (!empty($roleIdArr)) {
                // 将管理员的所有角色置为已删除
                $this->userRoleService->update(['user_id' => $id], ['status' => UserRoleConstant::USER_ROLE_STATUS_DELETE]);

                foreach ($roleIdArr as $k => $v) {
                    // 检查角色是否已删除
                    $this->roleLogic->checkRole($v);

                    if ($this->userRoleService->search(['user_id' => $id, 'role_id' => $v])) {
                        // 恢复角色权限
                        $this->userRoleService->update(['user_id' => $id, 'role_id' => $v], ['status' => UserRoleConstant::USER_ROLE_STATUS_NORMAL]);
                    } else {
                        // 创建角色权限
                        $this->userRoleService->create(['user_id' => $id, 'role_id' => $v]);
                    }
                }
            }

            $this->service->commit();
        } catch (\Exception $exception) {
            $this->service->rollBack();
            Log::error('更新管理员失败', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'requestData' => $requestData]);
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
        $id     = $requestData['id'];
        unset($requestData['id']);

        // 检查用户是否存在
        $user = $this->checkUser($id);
        // 检查 status 字段
        if (isset($requestData['status'])) {
            $this->checkUserStatus($requestData['status']);
        }
        // ROOT 用户不允许删除
        if ($user['root'] == UserConstant::IS_ROOT && isset($requestData['status']) && $requestData['status'] == UserConstant::USER_STATUS_DELETE) {
            throw new AppException(AppErrorCode::ROOT_USER_DELETE_ERROR);
        }

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
        $requestData['status'] = UserConstant::USER_STATUS_NORMAL;
        $list  = $this->service->search($requestData, $p, $size,
            ['id', 'name', 'email', 'mobile', 'position', 'mtime', 'ctime', 'sort', 'root'],
            ['root' => 'desc', 'sort' => 'asc', 'ctime' => 'desc']
        );

        $userRoleGroupByUserId = [];
        if (!empty($list)) {
            $userIdList     = array_column($list, 'id');
            $userRoleList   = $this->userRoleService->getUserRoleList($userIdList);

            if (!empty($userRoleList)) {
                foreach ($userRoleList as $k => $v) {
                    $userRoleGroupByUserId[$v['user_id']][] = $v;
                }
            }

            foreach ($list as $k => $v) {
                $list[$k]['role_list'] = isset($userRoleGroupByUserId[$v['id']]) ? $userRoleGroupByUserId[$v['id']] : [];
            }
        }

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
        $id = $requestData['id'];
        $user = $this->checkUser($id);
        unset($user['password']);
        $user['role_list'] = $this->userRoleService->getUserRoleList([$id]);
        return $user;
    }

    /**
     * 用户登录
     *
     * @param $requestData
     * @return array
     */
    public function login($requestData)
    {
        $email      = $requestData['email'];
        $password   = $requestData['password'];

        $user = $this->service->getLineByWhere([
            'email'     => $email,
            'status'    => UserConstant::USER_STATUS_NORMAL,
        ]);

        if (empty($user)) throw new AppException(AppErrorCode::USER_NOT_EXIST_ERROR);
        if ($user['password'] != self::encryptPassword($password)) throw new AppException(AppErrorCode::USER_PASSWORD_ERROR);

        $token = self::generateToken($user['id']);

        // 用户 token 写入缓存
        $this->service->writeTokenBuffer($token, $user['id']);

        return ['access_token' => $token, 'expire' => CommonConstant::TOKEN_EXPIRE_SECONDS];
    }

    /**
     * 退出登录
     *
     * @param $requestData
     * @return bool
     */
    public function logout($requestData)
    {
        $accessToken = $requestData['access_token'];
        $this->service->deleteTokenBuffer($accessToken);
        return true;
    }

    /**
     * 获取用户拥有的菜单
     *
     * @param $requestData
     * @return array|array[]
     */
    public function getUserMenuList($requestData)
    {
        $userId         = $requestData['user_id'];
        $emptyList      = ['list' => []];

        // 检查用户是否存在
        $this->checkUser($userId);

        // 获取用户的角色列表
        $userRoleList   = $this->userRoleService->getUserRoleList([$userId]);
        if (empty($userRoleList)) return $emptyList;

        // 管理员是否有超级管理员角色
        $hasAdminRole   = false;
        foreach ($userRoleList as $k => $v) {
            if ($v['admin'] == RoleConstant::ADMIN_YES) $hasAdminRole = true;
        }
        // 如果管理员有超级管理员角色，返回所有菜单
        if ($hasAdminRole) return $this->menuLogic->search([]);

        // 角色菜单
        $roleMenuList = $this->roleMenuService->getRoleMenuByIdList(array_column($userRoleList, 'role_id'));
        if (empty($roleMenuList)) return $emptyList;
        // 菜单去重
        $roleMenuList = Util::twoDimensionalArrayUnique($roleMenuList, 'id');

        // 一级菜单 ID 列表
        $classAMenuIdList = [];
        foreach ($roleMenuList as $k => $v) {
            unset($roleMenuList[$k]['role_id']);
            if (!in_array($v['pid'], $classAMenuIdList)) $classAMenuIdList[] = $v['pid'];
        }
        // 一级菜单列表（排序）
        $classAMenuList = $this->menuService->search(['id' => $classAMenuIdList, 'status' => MenuConstant::MENU_STATUS_NORMAL], 0, 0, ['*'], ['sort' => 'asc']);

        foreach ($classAMenuList as $classAMenuKey => $classAMenuVal) {
            $classAMenuList[$classAMenuKey]['sub_menu_list'] = [];

            foreach ($roleMenuList as $classBMenuKey => $classBMenuVal) {
                if ($classBMenuVal['pid'] == $classAMenuVal['id']) {
                    $classAMenuList[$classAMenuKey]['sub_menu_list'][] = $classBMenuVal;
                }
            }
        }

        return ['list' => $classAMenuList];
    }

    /**
     * 检查用户权限
     *
     * @param $token
     * @param $url
     * @return int
     */
    public function checkPermission($token, $url)
    {
        // 根据 access_token 获取用户 ID，如果从缓存中没有获取到用户 ID，抛出异常
        $userId         = $this->service->getUserIdByToken($token);
        $userId         = intval($userId);

        // 部分路由直接返回，不需要校验权限
        if (in_array($url, [
            '/',                            // 后台首页
            '/v1/user/menu',                // 左侧菜单接口
            '/v1/user/get_info',            // 用户基本信息
        ])) {
            return $userId;
        }

        // 用户角色
        $userRoleList   = $this->userRoleService->getUserRoleList([$userId]);
        // 用户角色为空，直接抛错
        if (empty($userRoleList)) throw new AppException(AppErrorCode::USER_ROLE_EMPTY_ERROR);

        // 用户是否有超级管理员角色
        $hasAdminRole   = false;
        foreach ($userRoleList as $k => $v) {
            if ($v['admin'] == RoleConstant::ADMIN_YES) $hasAdminRole = true;
        }
        // 如果用户有超级管理员角色，直接返回
        if ($hasAdminRole) return $userId;

        $roleIdList = array_column($userRoleList, 'role_id');

        // 角色对应的权限
        $permissionList = $this->rolePermissionService->getRolePermissionByIdList($roleIdList);
        // 角色权限为空，直接抛错
        if (empty($permissionList)) throw new AppException(AppErrorCode::USER_ROLE_PERMISSION_EMPTY_ERROR);
        // 权限去重
        $permissionList = Util::twoDimensionalArrayUnique($permissionList, 'permission_id');
        // 权限路由
        $urlList = [];
        foreach ($permissionList as $k => $v) {
            $tmpUrlList = explode(';', $v['url']);
            $urlList    = array_merge($urlList, $tmpUrlList);
        }
        // 权限路由去重
        $urlList = array_values(array_unique($urlList));

        // 请求的路由不在该用户拥有的权限列表中，则表示该用户无该路由的权限
        if (empty($urlList) || !in_array($url, $urlList)) throw new AppException(AppErrorCode::USER_PERMISSION_ERROR);

        return $userId;
    }
}