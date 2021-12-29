<?php

namespace App\Module\Stat\Action;

use App\Module\Stat\Logic\StatLogic;
use App\Util\HttpUtil;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class StatAction
{
    /**
     * @Inject()
     * @var StatLogic
     */
    private $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    private $rules = [

    ];

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $res = $this->logic->stat($requestData);
        return HttpUtil::success($response, $res);
    }
}