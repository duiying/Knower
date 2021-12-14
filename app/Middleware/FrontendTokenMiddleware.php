<?php

namespace App\Middleware;

use App\Constant\CommonConstant;
use App\Module\Account\Logic\AccountLogic;
use App\Util\HttpUtil;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 前台 Token 校验中间件
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Middleware
 */
class FrontendTokenMiddleware
{
    /**
     * @Inject()
     * @var AccountLogic
     */
    private $accountLogic;

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
        // 1、先检查 access_token 是否存在
        $tokenName = CommonConstant::FRONTEND_TOKEN_COOKIE_NAME;
        // 根据 access_token 检查用户权限
        $accessToken = $this->request->input($tokenName);
        // 请求参数中没有 access_token，尝试从 cookie 中获取 access_token
        if (empty($accessToken)) $accessToken = $this->request->cookie($tokenName);
        if (empty($accessToken)) {
            return HttpUtil::error($this->response, 403, '请先登录！');
        }

        // 2、再检查通过 access_token 是否能查到用户信息
        $accountInfo = $this->accountLogic->getAccountInfoByToken($accessToken);
        if (empty($accountInfo)) {
            return HttpUtil::error($this->response, 403, 'Token 无效，请重新登录！');
        }

        $accountId = $accountInfo['id'];

        // 在控制器中可以通过 $request->getAttribute('account_id') 获取当前登录的用户 ID
        $request = $request->withAttribute('account_id', $accountId);

        return $handler->handle($request);
    }
}