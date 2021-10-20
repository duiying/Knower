<?php

namespace App\Util;

use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;

/**
 * Redis 封装类
 *
 * Class Redis
 * @package App\Util
 */
class Redis
{
    /**
     * 获取 Redis 实例
     *
     * @param string $poolName
     * @return \Hyperf\Redis\RedisProxy|\Redis|null
     */
    public static function instance($poolName = 'default')
    {
        return ApplicationContext::getContainer()->get(RedisFactory::class)->get($poolName);
    }
}