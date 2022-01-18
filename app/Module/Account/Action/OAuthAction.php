<?php

namespace App\Module\Account\Action;

use App\Constant\CommonConstant;
use App\Module\Account\Logic\AccountLogic;
use App\Util\HttpUtil;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class OAuthAction
{
    /**
     * @Inject()
     * @var AccountLogic
     */
    public $logic;

    /**
     * 组装 GitHub 登录 URL 信息，并重定向到该 URL
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function github(RequestInterface $request, ResponseInterface $response)
    {
        $state = Util::generateToken();

        $this->logic->setGitHubStateCache($state);

        $clientId = env('GITHUB_CLIENT_ID');
        $redirectUri = env('GITHUB_REDIRECT_HOST') . '/oauth/github/callback';
        $redirectStr = sprintf('https://github.com/login/oauth/authorize?client_id=%s&redirect_uri=%s&state=%s', $clientId, $redirectUri, $state);
        return $response->redirect($redirectStr);
    }

    /**
     * GitHub 回调
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function githubCallback(RequestInterface $request, ResponseInterface $response)
    {
        $requestData = $request->all();
        Log::info('GitHub 回调信息', $requestData);
        if (!isset($requestData['code']) && !isset($requestData['state'])) {
            return HttpUtil::error($response);
        }

        $accessToken = $this->logic->githubCallback($requestData['code'], $requestData['state']);
        $expire = time() + CommonConstant::FRONTEND_TOKEN_SECONDS;
        $cookie = new Cookie(CommonConstant::FRONTEND_TOKEN_COOKIE_NAME, $accessToken, $expire);
        return $response->withCookie($cookie)->redirect('/');
    }

    /**
     * 组装 QQ 登录 URL 信息，并重定向到该 URL
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function qq(RequestInterface $request, ResponseInterface $response)
    {
        $state = Util::generateToken();

        $this->logic->setQQStateCache($state);

        $appId = env('QQ_APP_ID');
        $redirectUri = env('QQ_REDIRECT_HOST') . '/oauth/qq/callback';
        $redirectStr = sprintf('https://graph.qq.com/oauth2.0/authorize?response_type=code&scope=get_user_info&client_id=%s&redirect_uri=%s&state=%s', $appId, urlencode($redirectUri), $state);
        return $response->redirect($redirectStr);
    }

    /**
     * QQ 回调
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function qqCallback(RequestInterface $request, ResponseInterface $response)
    {
        $requestData = $request->all();
        Log::info('QQ 回调信息', $requestData);
        if (!isset($requestData['code']) && !isset($requestData['state'])) {
            return HttpUtil::error($response);
        }

        $accessToken = $this->logic->qqCallback($requestData['code'], $requestData['state']);
        $expire = time() + CommonConstant::FRONTEND_TOKEN_SECONDS;
        $cookie = new Cookie(CommonConstant::FRONTEND_TOKEN_COOKIE_NAME, $accessToken, $expire);
        return $response->withCookie($cookie)->redirect('/');
    }
}