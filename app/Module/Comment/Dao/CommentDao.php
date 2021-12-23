<?php

namespace App\Module\Comment\Dao;

use App\Module\AdminPassport\Role\Constant\RoleConstant;
use App\Module\AdminPassport\UserRole\Constant\UserRoleConstant;
use App\Module\Comment\Constant\CommentConstant;
use App\Util\MySQLDao;
use App\Util\Util;
use Hyperf\DbConnection\Db;

class CommentDao extends MySQLDao
{
    public $connection = 'content';
    public $table = 't_content_comment';

    /**
     * 获取评论数
     *
     * @param $thirdIdList
     * @param $thirdType
     * @return array
     */
    public function getCommentCount($thirdIdList, $thirdType)
    {
        $thirdIdList = array_unique($thirdIdList);
        if (empty($thirdIdList)) {
            return [];
        }

        $ids = implode(',', $thirdIdList);

        $status = CommentConstant::COMMENT_STATUS_NORMAL;

        $sql = "select count(*) as count, third_id from {$this->table} where third_id in ({$ids}) and third_type = {$thirdType} and `status` = {$status} GROUP BY third_id";

        $list = Db::connection($this->connection)->select($sql);

        return Util::objArr2Arr($list);
    }
}