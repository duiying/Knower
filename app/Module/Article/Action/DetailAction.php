<?php

namespace App\Module\Article\Action;

use App\Module\Article\Constant\ArticleConstant;
use App\Util\HttpUtil;
use App\Util\Util;
use App\Constant\CommonConstant;
use App\Module\Article\Logic\ArticleLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class DetailAction
{
    /**
     * @Inject()
     * @var ArticleLogic
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

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $requestData['status'] = ArticleConstant::ARTICLE_STATUS_NORMAL;

        $res = $this->logic->detail($requestData);
        return HttpUtil::success($response, $res);
    }
}