<?php

namespace App\Constant;

/**
 * redis key 常量类
 *
 * 所有的redis key统一使用常量定义在该类中，key必须要有注释
 *
 * key命名格式（）
 *      数据类型:服务简称:业务名称
 *
 * 数据类型
 *      string -> s
 *      hash -> h
 *      set -> s
 *      zset -> z
 *      list -> l
 *      geo -> g
 *
 * 服务简称
 *      ContentService -> cs
 *      AccountService -> as
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Constant
 */
class RedisKeyConst
{
    // 示例key
    const EXAMPLE_KEY = 's:服务简称:业务名称';
}