<?php

namespace App\Middleware;

use App\Util\HttpUtil;
use App\Util\Redis;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 防重放中间件
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Middleware
 */
class PreventRepeatMiddleware
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ContainerInterface $container, ResponseInterface $response, RequestInterface $request)
    {
        $this->container    = $container;
        $this->response     = $response;
        $this->request      = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        $preventRepeatKey = md5(json_encode($this->request->all()));
        $redis = Redis::instance();
        $noRepeat = $redis->set($preventRepeatKey, 1, ['nx', 'ex' => 3]);

        // 可能重放了，或者重复提交了，在这里防一手
        if (!$noRepeat) {
            return HttpUtil::error($this->response, 500, '请勿重复操作！');
        }

        return $handler->handle($request);
    }
}