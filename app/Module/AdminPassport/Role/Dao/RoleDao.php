<?php

namespace App\Module\AdminPassport\Role\Dao;

use App\Util\MySQLDao;

class RoleDao extends MySQLDao
{
    public $connection = 'passport';
    public $table = 't_passport_role';
}