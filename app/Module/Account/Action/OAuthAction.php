<?php

namespace App\Module\Account\Action;

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
        $expire = time() + 86400 * 365;
        $cookie = new Cookie('knower_access_token', $accessToken, $expire);
        return $response->withCookie($cookie)->redirect('/');
    }
}