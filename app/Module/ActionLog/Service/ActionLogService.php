<?php

namespace App\Module\ActionLog\Service;

use App\Module\ActionLog\Dao\ActionLogDao;
use Hyperf\Di\Annotation\Inject;

class ActionLogService
{
    /**
     * @Inject()
     * @var ActionLogDao
     */
    private $dao;

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        $this->dao->beginTransaction();
    }

    /**
     * 回滚事务
     */
    public function rollBack()
    {
        $this->dao->rollBack();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $this->dao->commit();
    }

    /**
     * 创建
     *
     * @param $data
     * @return int
     */
    public function create($data)
    {
        return $this->dao->create($data);
    }

    /**
     * 更新
     *
     * @param array $where
     * @param array $data
     * @return int
     */
    public function update($where = [], $data = [])
    {
        return $this->dao->update($where, $data);
    }

    /**
     * 查找
     *
     * @param array $where
     * @param int $p
     * @param int $size
     * @param string[] $columns
     * @param array $orderBy
     * @return array
     */
    public function search($where = [], $p = 0, $size = 0, $columns = ['*'], $orderBy = [])
    {
        return $this->dao->search($where, $p, $size, $columns, $orderBy);
    }

    /**
     * 获取一行
     *
     * @param array $where
     * @param string[] $columns
     * @param array $orderBy
     * @return array
     */
    public function getLineByWhere($where = [], $columns = ['*'], $orderBy = [])
    {
        return $this->dao->getLineByWhere($where, $columns, $orderBy);
    }

    /**
     * 统计
     *
     * @param array $where
     * @return int
     */
    public function count($where = [])
    {
        return $this->dao->count($where);
    }

    /**
     * 获取活跃游客数
     *
     * @param $beginTime
     * @param $endTime
     * @return int
     */
    public function getTouristCount($beginTime, $endTime)
    {
        return $this->dao->getTouristCount($beginTime, $endTime);
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
        return $this->dao->getActiveAccountCount($beginTime, $endTime);
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
        return $this->dao->getAccountActionLogCount($beginTime, $endTime, $type);
    }

    /**
     * 获取游客浏览量
     *
     * @param $beginTime
     * @param $endTime
     * @param int $type
     * @return int
     */
    public function getTouristActionLogCount($beginTime, $endTime, $type)
    {
        return $this->dao->getTouristActionLogCount($beginTime, $endTime, $type);
    }
}