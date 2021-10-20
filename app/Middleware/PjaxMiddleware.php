<?php

namespace App\Middleware;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * PJAX 中间件（局部刷新用）
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Middleware
 */
class PjaxMiddleware
{
    /**
     * PJAX 局部内容开始（需要在模板文件中增加这行注释作为开始位置）
     *
     * @var string
     */
    private $pjaxContentBegin   = '<!--  for pjax do not delete this line begin !!!  -->';

    /**
     * PJAX 局部内容结束（需要在模板文件中增加这行注释作为结束位置）
     *
     * @var string
     */
    private $pjaxContentEnd     = '<!--  for pjax do not delete this line end !!!  -->';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // 如果不是 PJAX 访问（浏览器直接刷新），直接返回
        if (!$request->hasHeader('X-PJAX-Container')) {
            return $response;
        }

        // 如果是登录页，直接返回
        if ($request->getUri()->getPath() === '/view/user/login') return $response;

        // 截取指定注释之间的 HTML 内容，作为返回的局部 HTML 内容，用于 PJAX 局部刷新用
        $html = new SwooleStream(
            self::getStrBetweenTwoStr($response->getBody()->getContents(), $this->pjaxContentBegin, $this->pjaxContentEnd)
        );

        return $response->withBody($html);
    }

    /**
     * 获取两个字符串之间的字符串
     *
     * @param $string
     * @param $needle1
     * @param $needle2
     * @return false|string
     */
    public static function getStrBetweenTwoStr($string, $needle1, $needle2)
    {
        $start  = stripos($string, $needle1) + strlen($needle1);
        $end    = stripos($string, $needle2);
        return substr($string, $start, $end - $start);
    }
}