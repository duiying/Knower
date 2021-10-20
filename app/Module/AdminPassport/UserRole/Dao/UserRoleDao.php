<?php

namespace App\Module\AdminPassport\UserRole\Dao;

use App\Module\AdminPassport\Role\Constant\RoleConstant;
use App\Module\AdminPassport\UserRole\Constant\UserRoleConstant;
use Hyperf\DbConnection\Db;
use App\Util\MySQLDao;
use App\Util\Util;

class UserRoleDao extends MySQLDao
{
    public $connection = 'passport';
    public $table = 't_passport_user_role';

    /**
     * 获取用户的角色列表
     *
     * @param array $userIdList
     * @return array
     */
    public function getUserRoleList($userIdList = [])
    {
        $sql = "select a.user_id, a.role_id, b.name, b.admin from t_passport_user_role a 
left join t_passport_role b on a.role_id = b.id
where a.user_id in (" . implode(',', $userIdList) . ") and a.status = ? and b.status = ? order by b.sort asc";

        $list = Db::connection($this->connection)->select($sql, [
            UserRoleConstant::USER_ROLE_STATUS_NORMAL,
            RoleConstant::ROLE_STATUS_NORMAL
        ]);

        return Util::objArr2Arr($list);
    }
}