<?php

namespace App\Module\User\Action;

use HyperfPlus\Controller\AbstractController;
use App\Module\User\Logic\UserLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use HyperfPlus\Http\Response;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use HyperfPlus\Util\Util;

class LoginAction extends AbstractController
{
    /**
     * @Inject()
     * @var UserLogic
     */
    private $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    private $rules = [
        'email'         => 'required|string',
        'password'      => 'required|string',
    ];

    public function handle(RequestInterface $request, Response $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $res = $this->logic->login($requestData);
        return $response->success($res, '登录成功！');
    }
}