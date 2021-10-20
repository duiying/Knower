<?php

namespace App\Util;

use Hyperf\Database\Query\Builder;
use Hyperf\DbConnection\Db;

class MySQLDao
{
    protected $connection = '';
    protected $table = '';

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        Db::connection($this->connection)->beginTransaction();
    }

    /**
     * 回滚事务
     */
    public function rollBack()
    {
        Db::connection($this->connection)->rollBack();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        Db::connection($this->connection)->commit();
    }

    /**
     * 创建
     *
     * @param array $data
     * @return int
     */
    public function create($data = [])
    {
        return Db::connection($this->connection)->table($this->table)->insertGetId($data);
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
        $table = $this->getBuilderByWhere($where);

        if (!empty($orderBy)) {
            foreach ($orderBy as $column => $direction) {
                $table->orderBy($column, $direction);
            }
        }

        $offset = ($p - 1) * $size;
        if ($offset)    $table->offset($offset);
        if ($size)      $table->limit($size);

        $list = $table->get($columns)->toArray();

        // 对象转数组
        if (!empty($list)) {
            array_walk($list, function (&$item) {
                $item = (array)$item;
            });
        }

        return $list;
    }

    /**
     * 统计
     *
     * @param array $where
     * @return int
     */
    public function count($where = [])
    {
        $table = $this->getBuilderByWhere($where);
        return $table->count();
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
        if (empty($data)) return 0;
        $table = $this->getBuilderByWhere($where);
        return $table->update($data);
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
        $list = $this->search($where, 0, 1, $columns, $orderBy);
        return $list ? (array)$list[0] : [];
    }

    /**
     * 根据条件构建查询构造器
     *
     * @param array $where
     * @return Builder
     */
    public function getBuilderByWhere($where = [])
    {
        $table = Db::connection($this->connection)->table($this->table);

        foreach ($where as $field => $value) {
            if (is_null($value)) continue;

            if (!is_array($value)) {
                $table->where($field, $value);
                continue;
            }

            switch ($value[0]) {
                case '=':   if (!is_array($value[1]))   $table->where($field, $value[1]); break;
                case '!=':
                case '<>':  if (!is_array($value[1]))   $table->where($field, '<>', $value[1]); break;
                case '%':   if (!is_array($value[1]))   $table->where($field, 'like', $value[1]); break;
                case '<':   if (!is_array($value[1]))   $table->where($field, '<', $value[1]); break;
                case '<=':  if (!is_array($value[1]))   $table->where($field, '<=', $value[1]); break;
                case '>':   if (!is_array($value[1]))   $table->where($field, '>', $value[1]); break;
                case '>=':  if (!is_array($value[1]))   $table->where($field, '>=', $value[1]); break;
                case '&':   if (is_array($value[1]))    $table->whereBetween($field, $value[1]); break;
                default:
                    $table->whereIn($field, $value);
            }
        }

        return $table;
    }
}