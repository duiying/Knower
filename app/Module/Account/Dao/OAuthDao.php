<?php

namespace App\Module\Account\Dao;

use App\Util\MySQLDao;

class OAuthDao extends MySQLDao
{
    public $connection = 'user';
    public $table = 't_user_oauth';
}