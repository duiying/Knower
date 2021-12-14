<?php

namespace App\Module\Comment\Dao;

use App\Util\MySQLDao;

class CommentDao extends MySQLDao
{
    public $connection = 'content';
    public $table = 't_content_comment';
}