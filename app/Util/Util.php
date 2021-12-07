<?php

namespace App\Util;

use App\Constant\AppErrorCode;
use App\Util\Exception\AppException;
use App\Util\Redis;

/**
 * 常用工具类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Util
 */
class Util
{
    /**
     * 对象转数组
     *
     * @param $obj
     * @return array
     */
    public static function object2Array($obj)
    {
        return json_decode(json_encode($obj), true);
    }

    /**
     * 格式化查找接口结果
     *
     * @param $p
     * @param $size
     * @param $total
     * @param $list
     * @return array
     */
    public static function formatSearchRes($p, $size, $total, $list)
    {
        return [
            'p'         => $p,
            'size'      => $size,
            'total'     => $total,
            'next'      => $p * $size < $total ? 1 : 0,
            'list'      => $list
        ];
    }

    /**
     * 一个字符串中是否包含另外一个字符串
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function contain($haystack, $needle)
    {
        return strstr($haystack, $needle) !== false;
    }

    /**
     * 协程休眠
     *
     * @param int $seconds
     */
    public static function sleep($seconds = 1)
    {
        \Swoole\Coroutine::sleep($seconds);
    }

    /**
     * 获取当前格式化时间
     *
     * @return string
     */
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 获取 traceId
     *
     * @return string
     */
    public static function getTraceId()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * 生成 Token
     *
     * @return string
     */
    public static function generateToken()
    {
        return md5(uniqid() . time() . mt_rand(10000, 99999));
    }

    /**
     * 获取 redis 分布式锁
     *
     * @param $key
     * @param int $timeout
     * @param string $poolName
     * @return bool
     */
    public static function getLock($key, $timeout = 1, $poolName = 'default')
    {
        return Redis::instance($poolName)->set($key, 1, ['nx', 'ex' => $timeout]);
    }

    /**
     * 删除 redis 分布式锁
     *
     * @param $key
     * @param string $poolName
     * @return int
     */
    public static function delLock($key, $poolName = 'default')
    {
        return Redis::instance($poolName)->del($key);
    }

    /**
     * 入队（基于 redis）
     *
     * @param $key
     * @param array $value
     * @param string $poolName
     * @return bool|int
     */
    public static function enqueueByRedis($key, $value = [], $poolName = 'default')
    {
        return Redis::instance($poolName)->lPush($key, json_encode($value));
    }

    /**
     * 出队（基于 redis）
     *
     * @param $key
     * @param string $poolName
     * @return mixed
     */
    public static function dequeueByRedis($key, $poolName = 'default')
    {
        $data = Redis::instance($poolName)->rPop($key);
        return json_decode($data, true);
    }

    /**
     * 将数组转换为对应的 redis key
     *
     * @param array $data
     * @return string
     */
    public static function generateRedisKeyByArrayData($data = [])
    {
        return md5(serialize($data));
    }

    /**
     * 清洗请求数据
     *
     * @param $requestData
     * @param $rules
     * @return array
     */
    public static function sanitize($requestData, $rules)
    {
        if (empty($requestData) || empty($rules)) return [];

        $sanitizedData = [];

        foreach ($requestData as $k => $v) {
            if (isset($rules[$k])) {
                // 对字符串类型的字段进行 trim 操作
                if (self::contain($rules[$k], 'required') && self::contain($rules[$k], 'string')) {
                    $v = trim($v);
                    if (empty($v)) throw new AppException(AppErrorCode::FIELD_EMPTY_EXCEPTION);
                }
                $sanitizedData[$k] = $v;
            }
        }

        return $sanitizedData;
    }

    /**
     * ids 转为 ID 数组
     *
     * @param $ids
     * @return array|false|string[]
     */
    public static function ids2IdArr($ids)
    {
        $ids = rtrim($ids, ',');
        if (empty($ids)) return [];
        return array_unique(explode(',', $ids));
    }

    /**
     * 对象数组转数组
     *
     * @param array $objList
     * @return array
     */
    public static function objArr2Arr($objList = [])
    {
        if (empty($objList)) return [];

        array_walk($objList, function (&$obj) {
            $obj = (array)$obj;
        });

        return $objList;
    }

    /**
     * 二维数组根据某个字段去重
     *
     * @param $arr
     * @param $column
     * @return array
     */
    public static function twoDimensionalArrayUnique($arr, $column)
    {
        if (empty($arr)) return [];

        $tmpArr = [];

        foreach ($arr as $k => $v) {
            if (!isset($tmpArr[$v[$column]])) {
                $tmpArr[$v[$column]] = $v;
            }
        }

        return array_values($tmpArr);
    }
}