<?php

namespace App\Module\AdminPassport\RolePermission\Dao;

use App\Module\AdminPassport\Permission\Constant\PermissionConstant;
use App\Module\AdminPassport\RolePermission\Constant\RolePermissionConstant;
use Hyperf\DbConnection\Db;
use App\Util\MySQLDao;
use App\Util\Util;

class RolePermissionDao extends MySQLDao
{
    public $connection = 'passport';
    public $table = 't_passport_role_permission';

    /**
     * 查找角色对应权限
     *
     * @param array $roleIdList
     * @return array
     */
    public function getRolePermissionByIdList($roleIdList = [])
    {
        $sql = "select a.role_id,a.permission_id,b.name,b.url from t_passport_role_permission a 
left join t_passport_permission b on a.permission_id = b.id
where a.role_id in (" . implode(',', $roleIdList) . ") and a.status = ? and b.status = ? order by b.sort asc";

        $list = Db::connection($this->connection)->select($sql, [
            RolePermissionConstant::ROLE_PERMISSION_STATUS_NORMAL,
            PermissionConstant::PERMISSION_STATUS_NORMAL
        ]);

        return Util::objArr2Arr($list);
    }
}