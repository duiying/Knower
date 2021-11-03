<?php

namespace App\Module\AdminPassport\Menu\Logic;

use App\Constant\AppErrorCode;
use App\Module\AdminPassport\Menu\Constant\MenuConstant;
use App\Util\Exception\AppException;
use Hyperf\Di\Annotation\Inject;
use App\Module\AdminPassport\Menu\Service\MenuService;

class MenuLogic
{
    /**
     * @Inject()
     * @var MenuService
     */
    private $service;

    /**
     * 检查菜单路由
     *
     * @param $requestData
     * @return mixed
     */
    public function checkMenuPid($requestData)
    {
        // 一级菜单不需要路由
        if ($requestData['pid'] == 0 && isset($requestData['url'])) {
            unset($requestData['url']);
            return $requestData;
        }

        // 二级菜单必须输入路由
        if ($requestData['pid'] != 0) {
            if (!isset($requestData['url']) || empty($requestData['url'])) throw new AppException(AppErrorCode::MENU_URL_EMPTY_ERROR);
        }

        return $requestData;
    }

    /**
     * 检查菜单是否存在并返回
     *
     * @param $menuId
     * @return array
     */
    public function checkMenu($menuId)
    {
        // 只允许添加二级菜单
        $menu = $this->service->getLineByWhere(['id' => $menuId, 'status' => MenuConstant::MENU_STATUS_NORMAL, 'pid' => ['>', 0]]);
        if (empty($menu)) throw new AppException(AppErrorCode::MENU_NOT_EXIST_ERROR);
        return $menu;
    }

    /**
     * 检查 status 字段
     *
     * @param $status
     */
    public function checkStatus($status)
    {
        if (!in_array($status, MenuConstant::ALLOWED_MENU_STATUS_LIST)) {
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
        $requestData = $this->checkMenuPid($requestData);
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
        $requestData    = $this->checkMenuPid($requestData);
        $id             = $requestData['id'];

        unset($requestData['id']);

        return $this->service->update(['id' => $id], $requestData);
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

        $menu = $this->service->getLineByWhere(['id' => $id, 'status' => MenuConstant::MENU_STATUS_NORMAL]);
        if (empty($menu)) throw new AppException(AppErrorCode::MENU_NOT_EXIST_ERROR);

        // 检查 status 字段
        if (isset($requestData['status'])) {
            $this->checkStatus($requestData['status']);
        }

        // 如果是删除一级菜单，需要先删除下面的二级菜单
        if (isset($requestData['status']) && $requestData['status'] == MenuConstant::MENU_STATUS_DELETE && $menu['pid'] == 0) {
            $classBMenuList = $this->service->search(['pid' => $menu['id'], 'status' => MenuConstant::MENU_STATUS_NORMAL]);
            if (!empty($classBMenuList)) throw new AppException(AppErrorCode::CLASS_A_MENU_DELETE_ERROR);
        }

        $this->service->update(['id' => $id], $requestData);
    }

    /**
     * 查找
     *
     * @param $requestData
     * @param $p
     * @param $size
     * @return array
     */
    public function search($requestData)
    {
        // 如果有 pid 参数，查询指定 pid 下的菜单列表
        if (isset($requestData['pid'])) {
            $classAMenuList = $this->service->search(['pid' => $requestData['pid'], 'status' => MenuConstant::MENU_STATUS_NORMAL]);
            return ['list' => $classAMenuList];
        }

        // 一级菜单列表
        $classAMenuList     = $this->service->search(['pid' => 0, 'status' => MenuConstant::MENU_STATUS_NORMAL]);
        // 二级菜单列表
        $classBMenuList     = $this->service->search(['pid' => ['>', 0], 'status' => MenuConstant::MENU_STATUS_NORMAL]);

        if (empty($classAMenuList)) return [];

        foreach ($classAMenuList as $classAMenuKey => $classAMenuVal) {
            $classAMenuList[$classAMenuKey]['sub_menu_list'] = [];

            foreach ($classBMenuList as $classBMenuKey => $classBMenuVal) {
                if ($classBMenuVal['pid'] == $classAMenuVal['id']) {
                    $classAMenuList[$classAMenuKey]['sub_menu_list'][] = $classBMenuVal;
                }
            }
        }

        return ['list' => $classAMenuList];
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