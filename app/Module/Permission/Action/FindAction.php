<?php

namespace App\Module\Permission\Action;

use HyperfPlus\Util\Util;
use HyperfPlus\Controller\AbstractController;
use App\Module\Permission\Logic\PermissionLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use HyperfPlus\Http\Response;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class FindAction extends AbstractController
{
    /**
     * @Inject()
     * @var PermissionLogic
     */
    private $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    private $rules = [
        'id' => 'required|integer'
    ];

    public function handle(RequestInterface $request, Response $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $res = $this->logic->find($requestData);
        return $response->success($res);
    }
}