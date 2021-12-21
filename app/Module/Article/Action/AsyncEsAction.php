<?php

namespace App\Module\Article\Action;

use App\Module\Article\Logic\ArticleLogic;
use App\Util\HttpUtil;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class AsyncEsAction
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

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        $res = $this->logic->asyncEs();
        return HttpUtil::success($response, $res);
    }
}