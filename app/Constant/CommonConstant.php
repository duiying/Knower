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
}