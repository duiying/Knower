<?php

namespace App\Module\User\Dao;

use App\Util\MySQLDao;

class UserDao extends MySQLDao
{
    public $connection = 'user';
    public $table = 't_user_account';
}