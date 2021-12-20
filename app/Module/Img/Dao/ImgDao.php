<?php

namespace App\Module\Img\Dao;

use App\Util\MySQLDao;

class ImgDao extends MySQLDao
{
    public $connection = 'content';
    public $table = 't_content_img';
}