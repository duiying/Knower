<?php

namespace App\Module\AdminPassport\RoleMenu\Dao;

use App\Module\AdminPassport\Menu\Constant\MenuConstant;
use App\Module\AdminPassport\Permission\Constant\PermissionConstant;
use App\Module\AdminPassport\RoleMenu\Constant\RoleMenuConstant;
use App\Module\AdminPassport\RolePermission\Constant\RolePermissionConstant;
use Hyperf\DbConnection\Db;
use App\Util\MySQLDao;
use App\Util\Util;

class RoleMenuDao extends MySQLDao
{
    public $connection = 'passport';
    public $table = 't_passport_role_menu';

    /**
     * 根据角色 ID 列表获取角色菜单
     *
     * @param $roleIdList
     * @return array
     */
    public function getRoleMenuByIdList($roleIdList)
    {
        $sql = "select a.role_id, b.* from t_passport_role_menu a 
left join t_passport_menu b on a.menu_id = b.id
where a.role_id in (" . implode(',', $roleIdList) . ") and a.status = ? and b.status = ? order by b.sort asc";

        $list = Db::connection($this->connection)->select($sql, [
            RoleMenuConstant::ROLE_MENU_STATUS_NORMAL,
            MenuConstant::MENU_STATUS_NORMAL
        ]);

        return Util::objArr2Arr($list);
    }
}