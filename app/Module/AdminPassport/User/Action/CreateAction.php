<?php

namespace App\Module\AdminPassport\User\Action;

use App\Util\HttpUtil;
use App\Util\Util;
use App\Module\AdminPassport\User\Logic\UserLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class CreateAction
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
        'name'          => 'required|string|max:50',
        'email'         => 'required|email|max:50',
        'mobile'        => 'required|string|max:20',
        'position'      => 'required|string|max:50',
        'password'      => 'required|string|min:6|max:32',
        'role_id'       => 'string',
    ];

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $res = $this->logic->create($requestData);
        return HttpUtil::success($response, $res);
    }
}