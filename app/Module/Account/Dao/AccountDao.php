<?php

namespace App\Module\Account\Dao;

use App\Util\MySQLDao;

class AccountDao extends MySQLDao
{
    public $connection = 'user';
    public $table = 't_user_account';
}