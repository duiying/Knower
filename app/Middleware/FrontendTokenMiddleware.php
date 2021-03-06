<?php

namespace App\Middleware;

use App\Constant\AppErrorCode;
use App\Constant\CommonConstant;
use App\Module\Account\Constant\AccountConstant;
use App\Module\Account\Logic\AccountLogic;
use App\Util\HttpUtil;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Cookie\Cookie;
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
        $fromApi = false;
        $contentType = $this->request->getHeaderLine('Content-Type');
        if (Util::contain($contentType, 'application/x-www-form-urlencoded') || Util::contain($contentType, 'application/json')) {
            $fromApi = true;
        }

        // 1、先检查 access_token 是否存在
        $tokenName = CommonConstant::FRONTEND_TOKEN_COOKIE_NAME;
        $accessToken = $this->request->input($tokenName);
        // 请求参数中没有 access_token，尝试从 cookie 中获取 access_token
        if (empty($accessToken)) $accessToken = $this->request->cookie($tokenName);
        if (empty($accessToken)) {
            if ($fromApi) {
                return HttpUtil::error($this->response, AppErrorCode::PLEASE_LOGIN, '请先登录！');
            } else {
                $cookie = new Cookie(CommonConstant::FRONTEND_TOKEN_COOKIE_NAME, '', time() - 3600);
                return $this->response->withCookie($cookie)->redirect('/login');
            }
        }

        // 2、再检查通过 access_token 是否能查到用户信息
        $accountInfo = $this->accountLogic->getAccountInfoByToken($accessToken);
        if (empty($accountInfo)) {
            if ($fromApi) {
                return HttpUtil::error($this->response, AppErrorCode::TOKEN_INVALID, 'Token 无效，请重新登录！');
            } else {
                $cookie = new Cookie(CommonConstant::FRONTEND_TOKEN_COOKIE_NAME, $accessToken, time() - 3600);
                return $this->response->withCookie($cookie)->redirect('/login');
            }
        }
        if ($accountInfo['status'] === AccountConstant::ACCOUNT_STATUS_FORBIDDEN) {
            if ($fromApi) {
                return HttpUtil::error($this->response, AppErrorCode::ACCOUNT_STATUS_FORBIDDEN, '账号已被停用！');
            } else {
                $cookie = new Cookie(CommonConstant::FRONTEND_TOKEN_COOKIE_NAME, $accessToken, time() - 3600);
                return $this->response->withCookie($cookie)->redirect('/login');
            }
        }
        if (strtotime($accountInfo['access_token_expire']) < time()) {
            if ($fromApi) {
                return HttpUtil::error($this->response, AppErrorCode::TOKEN_EXPIRED, 'Token 已过期，请重新登录！');
            } else {
                $cookie = new Cookie(CommonConstant::FRONTEND_TOKEN_COOKIE_NAME, $accessToken, time() - 3600);
                return $this->response->withCookie($cookie)->redirect('/login');
            }
        }

        $accountId = $accountInfo['id'];

        // 在控制器中可以通过 $request->getAttribute('account_id') 获取当前登录的用户 ID
        $request = $request->withAttribute('account_id', $accountId);
        // 更新用户最近活跃时间
        $this->accountLogic->refreshLastActiveTime($accountId);

        // 封装客户端真实 IP 信息
        $clientRealIp = HttpUtil::getClientRealIp($this->request);
        // 在控制器中可以通过 $request->getAttribute('client_real_ip') 获取当前用户的真实 IP 地址
        $request = $request->withAttribute('client_real_ip', $clientRealIp);

        return $handler->handle($request);
    }
}