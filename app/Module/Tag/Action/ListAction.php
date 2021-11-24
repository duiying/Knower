<?php

namespace App\Module\Tag\Action;

use App\Util\HttpUtil;
use App\Module\Tag\Logic\TagLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class ListAction
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
    ];

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        $res = $this->logic->list();
        return HttpUtil::success($response, $res);
    }
}