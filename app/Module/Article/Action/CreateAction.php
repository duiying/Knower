<?php

namespace App\Module\Article\Action;

use App\Util\HttpUtil;
use App\Util\Util;
use App\Module\Article\Logic\ArticleLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class CreateAction
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
        'title'         => 'required|string|max:255',
        'desc'          => 'required|string|max:255',
        'content'       => 'required|string',
        'sort'          => 'integer|min:1|max:999',
        'cover_img_id'  => 'integer',
    ];

    public function handle(RequestInterface $request, ResponseInterface $response, \League\Flysystem\Filesystem $filesystem)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);

        $res = $this->logic->create($requestData);
        return HttpUtil::success($response, $res);
    }
}