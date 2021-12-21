<?php

namespace App\Util;

use App\Constant\CommonConstant;
use Hyperf\HttpServer\Contract\ResponseInterface;

class HttpUtil
{
    /**
     * API 成功响应数据
     *
     * @param ResponseInterface $response
     * @param null $data
     * @param string $msg
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function success(ResponseInterface $response, $data = null, $msg = ''): \Psr\Http\Message\ResponseInterface
    {
        $response->withHeader('Content-Type', 'text/html; charset=utf-8');
        return $response->json([CommonConstant::API_CODE => 0, CommonConstant::API_MESSAGE => $msg, CommonConstant::API_DATA => $data]);
    }

    /**
     * API 失败响应数据
     *
     * @param ResponseInterface $response
     * @param int $code
     * @param string $msg
     * @param null $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function error(ResponseInterface $response, $code = 500, $msg = '', $data = null): \Psr\Http\Message\ResponseInterface
    {
        $response->withHeader('Content-Type', 'text/html; charset=utf-8');
        return $response->json([CommonConstant::API_CODE => $code, CommonConstant::API_MESSAGE => $msg, CommonConstant::API_DATA => $data]);
    }

    /**
     * 重定向
     *
     * @param ResponseInterface $response
     * @param $url
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function redirect(ResponseInterface $response, $url): \Psr\Http\Message\ResponseInterface
    {
        return $response->redirect($url);
    }
}