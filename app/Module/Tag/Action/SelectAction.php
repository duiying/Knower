<?php

namespace App\Module\Tag\Action;

use App\Module\Tag\Constant\TagConstant;
use App\Util\HttpUtil;
use App\Util\Util;
use App\Module\Tag\Logic\TagLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class SelectAction
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
        'name'          => 'string',
    ];

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        // 标签类型先写死为「文章」
        $requestData['type'] = TagConstant::TAG_TYPE_ARTICLE;
        // 标签状态为「正常」
        $requestData['status'] = TagConstant::TAG_STATUS_NORMAL;

        $res = $this->logic->search($requestData, 0, 0);
        return HttpUtil::success($response, $res);
    }
}