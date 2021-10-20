<?php

namespace App\Module\AdminPassport\User\Dao;

use App\Util\MySQLDao;

class UserDao extends MySQLDao
{
    public $connection = 'passport';
    public $table = 't_passport_user';
}