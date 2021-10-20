<?php

namespace App\Module\User\Action;

use HyperfPlus\Util\Util;
use HyperfPlus\Controller\AbstractController;
use App\Module\User\Logic\UserLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use HyperfPlus\Http\Response;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class CreateAction extends AbstractController
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
        'name'          => 'required|string',
        'email'         => 'required|string',
        'mobile'        => 'required|string',
        'position'      => 'required|string',
        'password'      => 'required|string',
        'role_id'       => 'string',
    ];

    public function handle(RequestInterface $request, Response $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $res = $this->logic->create($requestData);
        return $response->success($res);
    }
}