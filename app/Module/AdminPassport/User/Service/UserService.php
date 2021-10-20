<?php

namespace App\Module\AdminPassport\User\Service;

use App\Constant\AppErrorCode;
use App\Constant\CommonConstant;
use App\Constant\RedisKeyConst;
use Hyperf\Di\Annotation\Inject;
use App\Module\AdminPassport\User\Dao\UserDao;
use App\Util\Exception\AppException;
use App\Util\Redis;

class UserService
{
    /**
     * @Inject()
     * @var UserDao
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
     * 用户 token 写入缓存
     *
     * @param $token
     * @param $userId
     * @return bool
     */
    public function writeTokenBuffer($token, $userId)
    {
        $timeout = CommonConstant::TOKEN_EXPIRE_SECONDS;

        $redis = Redis::instance();

        // 清除之前的 token 缓存（如果有的话），保证管理员同时只有一个有效 token
        $prevToken = $redis->get(RedisKeyConst::USER_TOKEN . $userId);
        if (!empty($prevToken)) $redis->del($prevToken);

        // 缓存双写 （1）可以通过 token 找到管理员信息（2）可以通过管理员 ID 获取 token 信息
        $redis->set($token, $userId, $timeout);
        $redis->set(RedisKeyConst::USER_TOKEN . $userId, $token, $timeout);

        return true;
    }

    /**
     * 清除 token 缓存
     *
     * @param $token
     */
    public function deleteTokenBuffer($token)
    {
        $redis = Redis::instance();

        $userId = $redis->get($token);

        $redis->del($token);
        $redis->del(RedisKeyConst::USER_TOKEN . $userId);
    }

    /**
     * 根据用户 token 获取用户 ID
     *
     * @param $token
     * @return bool|mixed|string
     */
    public function getUserIdByToken($token)
    {
        $redis = Redis::instance();
        $userId = $redis->get($token);
        if (empty($userId)) throw new AppException(AppErrorCode::TOKEN_INVALID_ERROR);
        return $userId;
    }
}