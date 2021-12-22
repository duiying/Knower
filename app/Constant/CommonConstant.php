<?php

namespace App\Constant;

/**
 * 公共常量类
 *
 * @author Yaxian <wangyaxiandev@gmail.com>
 * @package App\Constant
 */
class CommonConstant
{
    const API_CODE                          = 'code';                       // API 接口 code 字段
    const API_MESSAGE                       = 'msg';                        // API 接口 msg 字段
    const API_DATA                          = 'data';                       // API 接口 data 字段

    const DEFAULT_PAGE                      = 1;                            // 默认页码
    const DEFAULT_SIZE                      = 20;                           // 默认每页大小

    const METHOD_GET                        = 'GET';                        // GET 请求
    const METHOD_POST                       = 'POST';                       // POST 请求

    // 管理员 token 有效时长（8 小时）
    const TOKEN_EXPIRE_SECONDS = 3600 * 8;

    // 前台登录 token
    const FRONTEND_TOKEN_COOKIE_NAME = 'knower_access_token';

    // Markdown 中的图片是否缓存到本地
    const MARKDOWN_IMG_CACHE = TRUE;
}