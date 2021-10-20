<?php

namespace App\Module\AdminPassport\Permission\Dao;

use App\Util\MySQLDao;

class PermissionDao extends MySQLDao
{
    public $connection = 'passport';
    public $table = 't_passport_permission';
}