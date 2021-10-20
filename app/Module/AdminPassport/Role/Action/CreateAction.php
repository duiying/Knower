<?php

namespace App\Module\AdminPassport\Role\Action;

use App\Util\HttpUtil;
use App\Util\Util;
use App\Module\AdminPassport\Role\Logic\RoleLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class CreateAction
{
    /**
     * @Inject()
     * @var RoleLogic
     */
    private $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    private $rules = [
        'name'          => 'required|string|max:50',
        'sort'          => 'integer|min:1|max:999',
        'permission_id' => 'string',
        'menu_id'       => 'string',
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