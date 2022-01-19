<?php

namespace App\Module\AdminPassport\Permission\Action;

use App\Util\HttpUtil;
use App\Module\AdminPassport\Permission\Logic\PermissionLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class SelectAction
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

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        $res = $this->logic->search([], 0, 0);
        return HttpUtil::success($response, $res);
    }
}