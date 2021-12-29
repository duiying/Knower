<?php

namespace App\Module\Account\Logic;

use App\Constant\AppErrorCode;
use App\Constant\RedisKeyConst;
use App\Module\Account\Constant\AccountConstant;
use App\Module\Account\Constant\OAuthConstant;
use App\Module\Account\Service\AccountService;
use App\Module\Account\Service\OAuthService;
use App\Module\Img\Logic\ImgLogic;
use App\Util\AppException;
use App\Util\HTTPClient;
use App\Util\Log;
use App\Util\Redis;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Coroutine;

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
     * @var ImgLogic
     */
    public $imgLogic;

    /**
     * @Inject()
     * @var OAuthService
     */
    public $oAuthService;

    /**
     * 检查 status 字段
     *
     * @param $status
     */
    public function checkStatus($status)
    {
        if (!in_array($status, AccountConstant::ALLOWED_STATUS_LIST)) {
            throw new AppException(AppErrorCode::PARAMS_INVALID, 'status 参数错误！');
        }
    }

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

        $avatarImgId = $this->imgLogic->findOrCreateImgByOriginUrl($avatar);

        // 1、先更新第三方登录表
        $updateOAuthData = [
            'token'         => $token,
            'avatar_img_id' => $avatarImgId
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
            'avatar_img_id' => $avatarImgId,
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
        $avatarImgId = $this->imgLogic->findOrCreateImgByOriginUrl($avatar);

        $this->accountService->beginTransaction();

        // 1、先创建一个用户
        $createAccountParams = [
            'nickname'              => $nickname,
            'email'                 => $email,
            'mobile'                => $mobile,
            'avatar_img_id'         => $avatarImgId,
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
            'avatar_img_id' => $avatarImgId
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

    /**
     * 根据 token 获取用户信息（前台使用）
     *
     * @param string $accessToken
     * @return array
     */
    public function getAccountInfoByToken($accessToken = '')
    {
        if (empty($accessToken)) return [];
        $accountInfo = $this->accountService->getLineByWhere(['access_token' => $accessToken], ['id', 'nickname', 'avatar_img_id', 'last_active_time', 'status']);
        $avatarImgId = $accountInfo['avatar_img_id'];
        $imgInfoMap = $this->imgLogic->getImgUrlMapByIdList([$avatarImgId]);
        $accountInfo['avatar'] = isset($imgInfoMap[$avatarImgId]) ? $imgInfoMap[$avatarImgId] : '';
        unset($accountInfo['avatar_img_id']);
        return $accountInfo;
    }

    /**
     * 根据用户 ID 获取用户基础信息（Map 结构）
     *
     * @param array $idList
     * @return array
     */
    public function getAccountInfoMapByIdList($idList = [])
    {
        // 用户 ID 去重
        $idList = array_filter(array_unique($idList));
        if (empty($idList)) return [];

        $accountInfoList = $this->accountService->search(['id' => $idList], 0, 0, ['id', 'nickname', 'avatar_img_id', 'last_active_time']);

        // 组装用户信息
        if (!empty($accountInfoList)) {
            $avatarImgIdList = array_column($accountInfoList, 'avatar_img_id');
            $imgUrlMap = $this->imgLogic->getImgUrlMapByIdList($avatarImgIdList);

            foreach ($accountInfoList as $k => $v) {
                $accountInfoList[$k]['avatar'] = isset($imgUrlMap[$v['avatar_img_id']]) ? $imgUrlMap[$v['avatar_img_id']] : '';
                unset($accountInfoList[$k]['avatar_img_id']);
            }
        }

        return array_column($accountInfoList, null, 'id');
    }

    /**
     * 用户列表
     *
     * @param $requestData
     * @param $p
     * @param $size
     * @return array
     */
    public function search($requestData, $p, $size)
    {
        $list  = $this->accountService->search($requestData, $p, $size,
            ['id', 'nickname', 'email', 'mobile', 'avatar_img_id', 'status', 'last_active_time', 'ctime'],
            ['ctime' => 'desc']
        );

        // 组装用户信息
        if (!empty($list)) {
            $avatarImgIdList = array_column($list, 'avatar_img_id');
            $imgInfoMap = $this->imgLogic->getImgUrlMapByIdList($avatarImgIdList);

            foreach ($list as $k => $v) {
                $list[$k]['avatar'] = isset($imgInfoMap[$v['avatar_img_id']]) ? $imgInfoMap[$v['avatar_img_id']] : '';
                $list[$k]['status_text'] = AccountConstant::ACCOUNT_STATUS_TEXT_MAP[$v['status']];
                unset($list[$k]['avatar_img_id']);
            }
        }

        $total = $this->accountService->count($requestData);
        return Util::formatSearchRes($p, $size, $total, $list);
    }

    /**
     * 更新字段
     *
     * @param $requestData
     * @return int
     */
    public function updateField($requestData)
    {
        $id = $requestData['id'];
        unset($requestData['id']);

        // 检查 status 字段
        if (isset($requestData['status'])) $this->checkStatus($requestData['status']);

        return $this->accountService->update(['id' => $id], $requestData);
    }

    /**
     * 用户总数量
     *
     * @return int
     */
    public function count()
    {
        return $this->accountService->count(['status' => AccountConstant::ACCOUNT_STATUS_NORMAL]);
    }

    /**
     * 更新用户最近活跃时间
     *
     * @param $accountId
     */
    public function refreshLastActiveTime($accountId)
    {
        Coroutine::create(function () use($accountId) {
            $this->accountService->update(['id' => $accountId], ['last_active_time' => Util::now()]);
        });
    }
}