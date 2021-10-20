<?php

namespace App\Module\AdminPassport\Permission\Action;

use App\Util\HttpUtil;
use App\Util\Util;
use App\Module\AdminPassport\Permission\Logic\PermissionLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class UpdateAction
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
        'id'            => 'required|integer',
        'name'          => 'required|string|max:50',
        'url'           => 'required|string|max:2000',
        'sort'          => 'integer|min:1|max:999'
    ];

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $requestData['mtime'] = Util::now();

        $res = $this->logic->update($requestData);
        return HttpUtil::success($response, $res);
    }
}