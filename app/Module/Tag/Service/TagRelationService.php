<?php

namespace App\Module\Tag\Service;

use App\Module\Tag\Dao\TagRelationDao;
use Hyperf\Di\Annotation\Inject;

class TagRelationService
{
    /**
     * @Inject()
     * @var TagRelationDao
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
     * 获取拥有的标签列表
     *
     * @param $thirdIdList
     * @param $type
     * @return array
     */
    public function getTagList($thirdIdList, $type)
    {
        return $this->dao->getTagList($thirdIdList, $type);
    }

    /**
     * 根据标签 ID 获取关联的第三方 ID
     *
     * @param $tagId
     * @return array
     */
    public function getThirdIdListByTagId($tagId)
    {
        return $this->dao->getThirdIdListByTagId($tagId);
    }
}