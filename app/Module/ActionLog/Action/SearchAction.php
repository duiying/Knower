<?php

namespace App\Module\ActionLog\Action;

use App\Module\ActionLog\Logic\ActionLogLogic;
use App\Util\HttpUtil;
use App\Util\Util;
use App\Constant\CommonConstant;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class SearchAction
{
    /**
     * @Inject()
     * @var ActionLogLogic
     */
    private $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    private $rules = [
        'p'             => 'integer|min:1',
        'size'          => 'integer|min:1',
        'account_id'    => 'integer',
    ];

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $p      = isset($requestData['p']) ? $requestData['p'] : CommonConstant::DEFAULT_PAGE;
        $size   = isset($requestData['size']) ? $requestData['size'] : CommonConstant::DEFAULT_SIZE;
        if (isset($requestData['p']))       unset($requestData['p']);
        if (isset($requestData['size']))    unset($requestData['size']);

        $res = $this->logic->search($requestData, $p, $size);
        return HttpUtil::success($response, $res);
    }
}