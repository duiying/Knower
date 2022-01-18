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
 *      PassportService -> ps
 *      UserService -> us
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Constant
 */
class RedisKeyConst
{
    // 示例 key
    const EXAMPLE_KEY = 's:服务简称:业务名称';

    // 后台登录 token
    const USER_TOKEN = 's:ps:token_user_id:';

    // 第三方登录相关
    const GITHUB_STATE = 's:us:github:state:';
    const QQ_STATE = 's:us:wechat:state:';

    // 全量同步 ES 限速
    const ASYNC_ES = 's:cs:async:es:limit';

    // IP2Region 缓存
    const IP_REGION = 's:us:ip:';
}