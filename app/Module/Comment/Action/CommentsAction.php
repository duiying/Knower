<?php

namespace App\Module\Comment\Action;

use App\Constant\CommonConstant;
use App\Module\Comment\Constant\CommentConstant;
use App\Module\Comment\Logic\CommentLogic;
use App\Util\HttpUtil;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class CommentsAction
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
        'p'             => 'integer|min:1',
        'size'          => 'integer|min:1',
        'third_id'      => 'required|integer|min:1',
        'third_type'    => 'integer',
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

        $p      = isset($requestData['p']) ? $requestData['p'] : CommonConstant::DEFAULT_PAGE;
        $size   = isset($requestData['size']) ? $requestData['size'] : CommonConstant::DEFAULT_SIZE;
        if (isset($requestData['p']))       unset($requestData['p']);
        if (isset($requestData['size']))    unset($requestData['size']);

        $res = $this->logic->comments($requestData, $p, $size);
        return HttpUtil::success($response, $res);
    }
}