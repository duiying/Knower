<?php

namespace App\Module\Article\Action;

use HyperfPlus\Util\Util;
use HyperfPlus\Controller\AbstractController;
use App\Module\Article\Logic\ArticleLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use HyperfPlus\Http\Response;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class UpdateAction extends AbstractController
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
        'id'            => 'required|integer',
        'title'         => 'required|string|max:255',
        'content'       => 'required|string',
        'sort'          => 'integer|min:1|max:999'
    ];

    public function handle(RequestInterface $request, Response $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $res = $this->logic->update($requestData);
        return $response->success($res);
    }
}