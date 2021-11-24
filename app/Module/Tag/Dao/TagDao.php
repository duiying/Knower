<?php

namespace App\Module\Tag\Dao;

use App\Util\MySQLDao;

class TagDao extends MySQLDao
{
    public $connection = 'content';
    public $table = 't_content_tag';
}