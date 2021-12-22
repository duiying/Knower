<?php

namespace App\Module\Comment\Action;

use App\Module\Comment\Logic\CommentLogic;
use App\Util\HttpUtil;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class CreateAction
{
    /**
     * @Inject()
     * @var CommentLogic
     */
    public $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    private $rules = [
        'third_id'      => 'required|integer',
        'content'       => 'required|string',
    ];

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);
        $requestData['client_real_ip']  = $request->getAttribute('client_real_ip');
        $requestData['account_id']      = $request->getAttribute('account_id');

        $res = $this->logic->create($requestData);
        return HttpUtil::success($response, $res);
    }
}