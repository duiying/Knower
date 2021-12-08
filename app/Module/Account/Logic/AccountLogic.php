<?php

namespace App\Module\Account\Logic;

use App\Constant\AppErrorCode;
use App\Constant\RedisKeyConst;
use App\Module\Account\Constant\OAuthConstant;
use App\Module\Account\Service\AccountService;
use App\Module\Account\Service\OAuthService;
use App\Util\AppException;
use App\Util\HTTPClient;
use App\Util\Log;
use App\Util\Redis;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use function GuzzleHttp\Promise\queue;

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
     * GitHub 登录回调
     *
     * @param $code
     * @param $state
     * @return string
     */
    public function githubCallback($code, $state)
    {
        if (!$this->checkGitHubStateCache($state)) {
            throw new AppException(AppErrorCode::PARAMS_INVALID, 'state 参数错误！');
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
            'connect_timeout'   => 3,
            'timeout'           => 3,
            'headers'           => ['Accept' => 'application/json'],
        ]);

        // GitHub 经常访问失败，增加重试
        for ($i = 0; $i < 2; $i++) {
            try {
                $getAccessTokenResponse = $client->request('POST', $getAccessTokenUrl, ['json' => $getAccessTokenParams]);
            } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
                Log::error('获取 GitHub access_token 异常！', ['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
                // 重试多次，依然失败，则抛出异常
                if ($i === 1) throw new AppException(AppErrorCode::GITHUB_ACCESS_TOKEN_FAIL);
            }
        }

        $getAccessTokenJsonStr = $getAccessTokenResponse->getBody()->getContents();
        Log::info('获取 GitHub access_token 返回信息：' . $getAccessTokenJsonStr);
        $getAccessTokenArr = json_decode($getAccessTokenJsonStr, true);

        if (!is_array($getAccessTokenArr) || !isset($getAccessTokenArr['access_token'])) {
            throw new AppException(AppErrorCode::GITHUB_TOKEN_INFO_ERROR);
        }

        // 2、根据 GitHub 返回的 access_token 获取用户信息
        $accessToken = $getAccessTokenArr['access_token'];
        $getGitHubUserInfoUrl = 'https://api.github.com/user';
        $client = $this->client->getClient([
            'connect_timeout'   => 3,
            'timeout'           => 3,
            'headers'           => ['Accept' => 'application/json', 'Authorization' => 'token ' . $accessToken],
        ]);

        // GitHub 经常访问失败，增加重试
        for ($i = 0; $i < 2; $i++) {
            try {
                $getGitHubUserInfoResponse = $client->request('GET', $getGitHubUserInfoUrl);
            } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
                Log::error('获取 GitHub 用户信息异常！', ['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
                // 重试多次，依然失败，则抛出异常
                if ($i === 1) throw new AppException(AppErrorCode::GITHUB_GET_USER_INFO_FAIL);
            }
        }

        $getGitHubUserInfoStr = $getGitHubUserInfoResponse->getBody()->getContents();
        Log::info('获取 GitHub 用户信息返回信息：' . $getGitHubUserInfoStr);
        $getGitHubUserInfoArr = json_decode($getGitHubUserInfoStr, true);

        if (!is_array($getGitHubUserInfoArr) || !isset($getGitHubUserInfoArr['id'])) {
            Log::error('获取 GitHub 用户信息返回异常：' . $getGitHubUserInfoStr);
            throw new AppException(AppErrorCode::GITHUB_USER_INFO_ERROR);
        }

        $gitHubId = $getGitHubUserInfoArr['id'];
        $gitHubName = $getGitHubUserInfoArr['login'];
        $gitHubEmail = $getGitHubUserInfoArr['email'];
        $gitHubAvatar = $getGitHubUserInfoArr['avatar_url'];

        // 检查是否已经注册
        $oAuthId = $this->checkIfRegisterByOAuth(OAuthConstant::OAUTH_TYPE_GITHUB, $gitHubId);
        if ($oAuthId === 0) {
            $accountToken = $this->register(OAuthConstant::OAUTH_TYPE_GITHUB, $gitHubId, $accessToken, $gitHubAvatar, $gitHubName, $gitHubEmail);
        } else {
            $accountToken = $this->refreshLoginInfo($oAuthId, $accessToken, $gitHubAvatar, $gitHubName, $gitHubEmail);
        }

        return $accountToken;
    }

    /**
     * 开始刷新用户登录信息
     *
     * @param $id
     * @param $token
     * @param string $avatar
     * @param string $nickname
     * @param string $email
     * @param string $mobile
     * @return string
     */
    public function refreshLoginInfo($id, $token, $avatar = '', $nickname = '', $email = '', $mobile = '')
    {
        Log::info('开始刷新用户登录信息', ['id' => $id, 'token' => $token, 'avatar' => $avatar, 'nickname' => $nickname, 'email' => $email, 'mobile' => $mobile]);

        // 1、先更新第三方登录表
        $updateOAuthData = [
            'token'     => $token,
            'avatar'    => $avatar
        ];
        $this->oAuthService->update(['id' => $id], $updateOAuthData);

        // 2、再更新用户表
        $oAuthInfo = $this->oAuthService->getLineByWhere(['id' => $id], ['id', 'account_id']);
        if (empty($oAuthInfo)) {
            throw new AppException(AppErrorCode::USER_REGISTER_INFO_NOT_EXIST);
        }
        $accessToken = Util::generateToken();
        $accountId = $oAuthInfo['account_id'];
        $updateAccountData = [
            'access_token'  => $accessToken,
            'avatar'        => $avatar,
            'nickname'      => $nickname,
            'email'         => $email,
            'mobile'        => $mobile
        ];
        $this->accountService->update(['id' => $accountId], $updateAccountData);

        return $accessToken;
    }

    /**
     * 用户注册
     *
     * @param $oAuthType
     * @param $oAuthId
     * @param $token
     * @param string $avatar
     * @param string $nickname
     * @param string $email
     * @param string $mobile
     * @return string
     */
    public function register($oAuthType, $oAuthId, $token, $avatar = '', $nickname = '', $email = '', $mobile = '')
    {
        Log::info('开始注册了', ['oAuthType' => $oAuthType, 'oAuthId' => $oAuthId,
            'token' => $token, 'avatar' => $avatar, 'nickname' => $nickname, 'email' => $email, 'mobile' => $mobile]);

        $accessToken = Util::generateToken();

        $this->accountService->beginTransaction();

        // 1、先创建一个用户
        $createAccountParams = [
            'nickname'              => $nickname,
            'email'                 => $email,
            'mobile'                => $mobile,
            'avatar'                => $avatar,
            'password'              => '',
            'access_token'          => $accessToken,
            'last_active_time'      => Util::now()
        ];
        $accountInsertId = $this->accountService->create($createAccountParams);

        // 2、再写入一条第三方登录
        $createOAuthParams = [
            'account_id'    => $accountInsertId,
            'oauth_type'    => $oAuthType,
            'oauth_id'      => $oAuthId,
            'token'         => $token,
            'avatar'        => $avatar
        ];
        $oAuthInsertId = $this->oAuthService->create($createOAuthParams);

        if (!$accountInsertId || !$oAuthInsertId) {
            $this->accountService->rollBack();
            Log::error('用户注册失败！', ['createAccountParams' => $createAccountParams, 'createOAuthParams' => $createOAuthParams]);
            throw new AppException(AppErrorCode::USER_REGISTER_FAIL);
        }

        $this->accountService->commit();

        return $accessToken;
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
        Log::info('根据第三方登录信息检查是否登录', ['oAuthType' => $oAuthType, 'oAuthId' => $oAuthId]);

        $oAuthInfo = $this->oAuthService->getLineByWhere([
            'oauth_type' => $oAuthType,
            'oauth_id' => $oAuthId
        ], ['id']);
        return isset($oAuthInfo['id']) ? intval($oAuthInfo['id']) : 0;
    }
}