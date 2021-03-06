<?php

namespace App\Module\Article\Action;

use App\Module\Article\Logic\ArticleLogic;
use App\Util\HttpUtil;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class InfoAction
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

        $requestData['client_real_ip']  = $request->getAttribute('client_real_ip');
        $requestData['account_id']      = $request->getAttribute('account_id');

        $res = $this->logic->find($requestData, true);
        return HttpUtil::success($response, $res);
    }
}