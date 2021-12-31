<?php

namespace App\Module\ActionLog\Dao;

use App\Util\Log;
use App\Util\MySQLDao;
use App\Util\Util;
use Hyperf\DbConnection\Db;

class ActionLogDao extends MySQLDao
{
    public $connection = 'user';
    public $table = 't_user_action_log';

    /**
     * 获取活跃游客数
     *
     * @param $beginTime
     * @param $endTime
     * @return int
     */
    public function getTouristCount($beginTime, $endTime)
    {
        $sql = "select count(distinct ip) as count from {$this->table} where account_id = 0 and ctime >= '{$beginTime}' and ctime < '{$endTime}'";
        $res = Db::connection($this->connection)->select($sql);
        $res = Util::objArr2Arr($res);
        return isset($res[0]['count']) ? intval($res[0]['count']) : 0;
    }

    /**
     * 获取活跃登录用户数
     *
     * @param $beginTime
     * @param $endTime
     * @return int
     */
    public function getActiveAccountCount($beginTime, $endTime)
    {
        $sql = "select count(distinct account_id) as count from {$this->table} where account_id > 0 and ctime >= '{$beginTime}' and ctime < '{$endTime}'";
        $res = Db::connection($this->connection)->select($sql);
        $res = Util::objArr2Arr($res);
        return isset($res[0]['count']) ? intval($res[0]['count']) : 0;
    }

    /**
     * 获取登录用户浏览量
     *
     * @param $beginTime
     * @param $endTime
     * @param int $type
     * @return int
     */
    public function getAccountActionLogCount($beginTime, $endTime, $type = 0)
    {
        $sql = "select count(*) as count from {$this->table} where account_id > 0 and ctime >= '{$beginTime}' and ctime < '{$endTime}'";
        if ($type > 0) {
            $sql .= " and type = {$type}";
        }
        $res = Db::connection($this->connection)->select($sql);
        $res = Util::objArr2Arr($res);
        return isset($res[0]['count']) ? intval($res[0]['count']) : 0;
    }

    /**
     * 获取游客浏览量
     *
     * @param $beginTime
     * @param $endTime
     * @param int $type
     * @return int
     */
    public function getTouristActionLogCount($beginTime, $endTime, $type = 0)
    {
        $sql = "select count(*) as count from {$this->table} where account_id = 0 and ctime >= '{$beginTime}' and ctime < '{$endTime}'";
        if ($type > 0) {
            $sql .= " and type = {$type}";
        }
        $res = Db::connection($this->connection)->select($sql);
        $res = Util::objArr2Arr($res);
        return isset($res[0]['count']) ? intval($res[0]['count']) : 0;
    }
}