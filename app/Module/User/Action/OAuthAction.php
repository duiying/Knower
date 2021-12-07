<?php

namespace App\Module\User\Action;

use App\Constant\RedisKeyConst;
use App\Util\HTTPClient;
use App\Util\HttpUtil;
use App\Util\Log;
use App\Util\Redis;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class OAuthAction
{
    /**
     * @Inject()
     * @var HTTPClient
     */
    public $client;

    /**
     * GitHub state 写入缓存
     *
     * @param $state
     */
    public function setGitHubStateCache($state)
    {
        $redis = Redis::instance();
        $redis->set(RedisKeyConst::GITHUB_STATE . $state, 1, ['nx', 'ex' => 30 * 60]);
    }

    /**
     * 检查 GitHub state
     *
     * @param $state
     * @return bool
     */
    public function checkGitHubStateCache($state)
    {
        if (empty($state)) {
            return false;
        }
        $redis = Redis::instance();
        $val = $redis->get(RedisKeyConst::GITHUB_STATE . $state);
        return intval($val) === 1;
    }

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

        $this->setGitHubStateCache($state);

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
        if (!isset($requestData['code']) && !isset($requestData['state']) || !$this->checkGitHubStateCache($requestData['state'])) {
            return HttpUtil::error($response);
        }

        $getAccessTokenUrl          = 'https://github.com/login/oauth/access_token';
        $clientId                   = env('GITHUB_CLIENT_ID');
        $secret                     = env('GITHUB_SECRET');
        $code                       = $requestData['code'];

        $getAccessTokenParams = [
            'client_id'     => $clientId,
            'client_secret' => $secret,
            'code'          => $code,
        ];

        $client = $this->client->getClient([
            'connect_timeout'   => 5,
            'timeout'           => 5,
            'headers'           => ['Accept' => 'application/json'],
        ]);

        try {
            $getAccessTokenResponse = $client->request('POST', $getAccessTokenUrl, ['json' => $getAccessTokenParams]);
        } catch (\Exception $exception) {
            Log::error('获取 GitHub access_token 异常！', ['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
            return HttpUtil::error($response);
        }

        $getAccessTokenJsonStr = $getAccessTokenResponse->getBody()->getContents();
        Log::info('获取 GitHub access_token 返回信息：' . $getAccessTokenJsonStr);
        $getAccessTokenArr = json_decode($getAccessTokenJsonStr, true);

        return $response->redirect('/');
    }
}