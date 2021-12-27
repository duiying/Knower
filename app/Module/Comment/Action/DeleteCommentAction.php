<?php

namespace App\Module\Comment\Action;

use App\Util\HttpUtil;
use App\Util\Util;
use App\Module\Comment\Logic\CommentLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class DeleteCommentAction
{
    /**
     * @Inject()
     * @var CommentLogic
     */
    private $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    private $rules = [
        'id'        => 'required|integer',
    ];

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);
        $requestData['client_real_ip']  = $request->getAttribute('client_real_ip');
        $requestData['account_id'] = $request->getAttribute('account_id');
        $res = $this->logic->deleteComment($requestData);
        return HttpUtil::success($response, $res);
    }
}