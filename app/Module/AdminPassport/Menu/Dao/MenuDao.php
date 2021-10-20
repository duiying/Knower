<?php

namespace App\Module\AdminPassport\Menu\Dao;

use App\Util\MySQLDao;

class MenuDao extends MySQLDao
{
    public $connection = 'passport';
    public $table = 't_passport_menu';
}