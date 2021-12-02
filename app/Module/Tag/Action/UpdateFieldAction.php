<?php

namespace App\Module\Tag\Action;

use App\Util\HttpUtil;
use App\Util\Util;
use App\Module\Tag\Logic\TagLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class UpdateFieldAction
{
    /**
     * @Inject()
     * @var TagLogic
     */
    private $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    private $rules = [
        'id'        => 'required|integer',
        'status'    => 'integer',
    ];

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $requestData['mtime'] = Util::now();

        $res = $this->logic->updateField($requestData);
        return HttpUtil::success($response, $res);
    }
}