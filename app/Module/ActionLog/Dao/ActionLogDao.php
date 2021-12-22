<?php

namespace App\Module\ActionLog\Dao;

use App\Util\MySQLDao;

class ActionLogDao extends MySQLDao
{
    public $connection = 'user';
    public $table = 't_user_action_log';
}