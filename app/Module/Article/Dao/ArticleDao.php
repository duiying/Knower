<?php

namespace App\Module\Article\Dao;

use App\Util\MySQLDao;

class ArticleDao extends MySQLDao
{
    public $connection = 'content';
    public $table = 't_content_article';
}