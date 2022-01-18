<?php

namespace App\Module\Account\Action;

use App\Constant\CommonConstant;
use App\Module\Account\Logic\AccountLogic;
use App\Util\HttpUtil;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class LogoutAction
{
    /**
     * @Inject()
     * @var AccountLogic
     */
    public $logic;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        $accessToken = $request->cookie(CommonConstant::FRONTEND_TOKEN_COOKIE_NAME, '');
        if (empty($accessToken)) {
            return $response->redirect('/');
        }
        $this->logic->logout($accessToken);
        $cookie = new Cookie(CommonConstant::FRONTEND_TOKEN_COOKIE_NAME, $accessToken, time() - 3600);
        return $response->withCookie($cookie)->redirect('/');
    }
}