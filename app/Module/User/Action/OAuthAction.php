<?php

namespace App\Module\User\Action;

use App\Util\HttpUtil;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class OAuthAction
{
    public function github(RequestInterface $request, ResponseInterface $response)
    {
        $clientId = env('GITHUB_CLIENT_ID');
        $redirectUri = env('GITHUB_REDIRECT_HOST') . '/oauth/github/callback';
        $redirectStr = sprintf('https://github.com/login/oauth/authorize?client_id=%s&redirect_uri=%s&state=111', $clientId, $redirectUri);
        return $response->redirect($redirectStr);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function githubCallback(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        if (!isset($requestData['code']) && !isset($requestData['state'])) {
            return HttpUtil::error($response);
        }
        Log::info('GitHub 回调信息', $requestData);
        return $response->redirect('/');
    }
}