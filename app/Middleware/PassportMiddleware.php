<?php

namespace App\Middleware;

use App\Module\AdminPassport\User\Logic\UserLogic;
use App\Util\HttpUtil;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use App\Util\Log;
use App\Util\Util;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PassportMiddleware
{
    /**
     * @Inject()
     * @var UserLogic
     */
    private $userLogic;

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

    /**
     * 不需要检验 access_token 的路由
     *
     * @var string[]
     */
    private $noCheckAccessTokenMethod = [
        '/v1/user/login',
        '/view/user/login',
    ];

    public function __construct(ContainerInterface $container, ResponseInterface $response, RequestInterface $request)
    {
        $this->container    = $container;
        $this->response     = $response;
        $this->request      = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        // 路由
        $requestPath = $this->request->getUri()->getPath();

        // 不需要检验 access_token 的路由
        if (in_array($requestPath, $this->noCheckAccessTokenMethod)) return $handler->handle($request);

        // 请求是否来自于 API
        $fromApi = true;
        if ($requestPath === '/' || (Util::contain($requestPath, '/view/'))) $fromApi = false;

        // 根据 access_token 检查用户权限
        $accessToken = $this->request->input('access_token');
        // 请求参数中没有 access_token，尝试从 cookie 中获取 access_token
        if (empty($accessToken)) $accessToken = $this->request->cookie('access_token');
        if (empty($accessToken)) {
            // 如果是视图渲染，重定向到登录页；如果是接口，返回接口响应数据
            return $fromApi ? HttpUtil::error($this->response, 403, '请先登录！') : $this->response->redirect('/view/user/login');
        }

        try {
            // 检查用户 access_token 以及权限
            $userId = $this->userLogic->checkPermission($accessToken, $requestPath);
        } catch (\Exception $exception) {
            Log::error('权限校验失败！', ['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
            return HttpUtil::error($this->response, $exception->getCode(), $exception->getMessage());
        }

        // 在控制器中可以通过 $request->getAttribute('user_id') 获取当前登录的用户 ID
        $request = $request->withAttribute('user_id', $userId);

        return $handler->handle($request);
    }
}