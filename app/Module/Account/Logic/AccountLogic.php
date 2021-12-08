<?php

namespace App\Module\Account\Logic;

use App\Constant\RedisKeyConst;
use App\Module\Account\Constant\OAuthConstant;
use App\Module\Account\Service\AccountService;
use App\Module\Account\Service\OAuthService;
use App\Util\HTTPClient;
use App\Util\Log;
use App\Util\Redis;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;

class AccountLogic
{
    /**
     * @Inject()
     * @var HTTPClient
     */
    public $client;

    /**
     * @Inject()
     * @var AccountService
     */
    public $accountService;

    /**
     * @Inject()
     * @var OAuthService
     */
    public $oAuthService;

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
     * GitHub 回调
     *
     * @param $code
     * @param $state
     * @return bool
     */
    public function githubCallback($code, $state)
    {
        if (!$this->checkGitHubStateCache($state)) {
            return false;
        }

        // 1、根据 GitHub 回调信息中的 code 信息去获取 GitHub 的 access_token
        $getAccessTokenUrl          = 'https://github.com/login/oauth/access_token';
        $clientId                   = env('GITHUB_CLIENT_ID');
        $secret                     = env('GITHUB_SECRET');

        $getAccessTokenParams = [
            'client_id'     => $clientId,
            'client_secret' => $secret,
            'code'          => $code,
        ];

        $client = $this->client->getClient([
            'connect_timeout'   => 10,
            'timeout'           => 10,
            'headers'           => ['Accept' => 'application/json'],
        ]);

        try {
            $getAccessTokenResponse = $client->request('POST', $getAccessTokenUrl, ['json' => $getAccessTokenParams]);
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            Log::error('获取 GitHub access_token 异常！', ['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
            return false;
        }

        $getAccessTokenJsonStr = $getAccessTokenResponse->getBody()->getContents();
        Log::info('获取 GitHub access_token 返回信息：' . $getAccessTokenJsonStr);
        $getAccessTokenArr = json_decode($getAccessTokenJsonStr, true);

        if (!is_array($getAccessTokenArr) || !isset($getAccessTokenArr['access_token'])) {
            return false;
        }

        // 2、根据 GitHub 返回的 access_token 获取用户信息
        $accessToken = $getAccessTokenArr['access_token'];
        $getGitHubUserInfoUrl = 'https://api.github.com/user';
        $client = $this->client->getClient([
            'connect_timeout'   => 10,
            'timeout'           => 10,
            'headers'           => ['Accept' => 'application/json', 'Authorization' => 'token ' . $accessToken],
        ]);

        try {
            $getGitHubUserInfoResponse = $client->request('GET', $getGitHubUserInfoUrl);
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            Log::error('获取 GitHub 用户信息异常！', ['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
            return false;
        }

        $getGitHubUserInfoStr = $getGitHubUserInfoResponse->getBody()->getContents();
        Log::info('获取 GitHub 用户信息返回信息：' . $getGitHubUserInfoStr);
        $getGitHubUserInfoArr = json_decode($getGitHubUserInfoStr, true);

        if (!is_array($getGitHubUserInfoArr) || !isset($getGitHubUserInfoArr['id'])) {
            return false;
        }

        $gitHubId = $getGitHubUserInfoArr['id'];
        $gitHubName = $getGitHubUserInfoArr['login'];
        $gitHubEmail = $getGitHubUserInfoArr['email'];
        $gitHubAvatar = $getGitHubUserInfoArr['avatar_url'];

        // 检查是否已经注册
        $oAuthId = $this->checkIfRegisterByOAuth(OAuthConstant::OAUTH_TYPE_GITHUB, $gitHubId);
        if ($oAuthId === 0) {
            $this->register(OAuthConstant::OAUTH_TYPE_GITHUB, $gitHubId, $accessToken, $gitHubAvatar, $gitHubName, $gitHubEmail);
        } else {
            $this->refreshLoginInfo($oAuthId, $accessToken, $gitHubAvatar, $gitHubName, $gitHubEmail);
        }

        return true;
    }

    public function refreshLoginInfo($oAuthId, $token, $avatar = '', $nickname = '', $email = '', $mobile = '')
    {

    }

    public function register($oAuthType, $oAuthId, $token, $avatar = '', $nickname = '', $email = '', $mobile = '')
    {
        Log::info(sprintf('开始注册了，oAuthType：%d，oAuthId：%s，token：%s avatar：%s，nickname：%s，email：%s，mobile：%s',
            $oAuthType, $oAuthId, $token, $avatar, $nickname, $email, $mobile));

        $accessToken = Util::generateToken();

        $createAccountParams = [
            'nickname'              => $nickname,
            'email'                 => $email,
            'mobile'                => $mobile,
            'avatar'                => $avatar,
            'password'              => '',
            'access_token'          => $accessToken,
            'last_active_time'      => Util::now()
        ];
        $accountId = $this->accountService->create($createAccountParams);

        $createOAuthParams = [
            'account_id'    => $accountId,
            'oauth_type'    => $oAuthType,
            'oauth_id'      => $oAuthId,
            'token'         => $token,
            'avatar'        => $avatar
        ];
        $this->oAuthService->create($createOAuthParams);
    }

    /**
     * 根据第三方登录信息检查是否登录
     *
     * @param $oAuthType
     * @param $oAuthId
     * @return int
     */
    public function checkIfRegisterByOAuth($oAuthType, $oAuthId)
    {
        $oAuthInfo = $this->oAuthService->getLineByWhere([
            'oauth_type' => $oAuthType,
            'oauth_id' => $oAuthId
        ], ['id']);
        return isset($oAuthInfo['id']) ? intval($oAuthInfo['id']) : 0;
    }
}