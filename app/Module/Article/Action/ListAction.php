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

class ListAction
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
        'p'             => 'integer|min:1',
        'size'          => 'integer|min:1',
        'id'            => 'integer',
        'keywords'      => 'string',
        'status'        => 'integer'
    ];

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        // 前台只展示正常状态的文章
        $requestData['status'] = ArticleConstant::ARTICLE_STATUS_NORMAL;

        $p      = isset($requestData['p']) ? $requestData['p'] : CommonConstant::DEFAULT_PAGE;
        $size   = isset($requestData['size']) ? $requestData['size'] : CommonConstant::DEFAULT_SIZE;
        if (isset($requestData['p']))       unset($requestData['p']);
        if (isset($requestData['size']))    unset($requestData['size']);

        $res = $this->logic->search($requestData, $p, $size);
        return HttpUtil::success($response, $res);
    }
}