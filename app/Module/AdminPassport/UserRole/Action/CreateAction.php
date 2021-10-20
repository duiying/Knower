<?php

namespace App\Module\AdminPassport\UserRole\Action;

use App\Util\HttpUtil;
use App\Util\Util;
use App\Module\AdminPassport\UserRole\Logic\UserRoleLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class CreateAction
{
    /**
     * @Inject()
     * @var UserRoleLogic
     */
    private $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    private $rules = [
        'user_id'       => 'required|integer',
        'role_id'       => 'required|integer'
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