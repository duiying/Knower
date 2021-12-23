<?php

namespace App\Module\Article\Dao;

use App\Util\MySQLDao;
use Hyperf\DbConnection\Db;

class ArticleDao extends MySQLDao
{
    public $connection = 'content';
    public $table = 't_content_article';

    /**
     * 阅读数 +1
     *
     * @param $id
     * @return int
     */
    public function incrReadCount($id)
    {
        $sql = "update {$this->table} set read_count = read_count + 1 where id = {$id}";
        return Db::connection($this->connection)->update($sql);
    }
}