<?php

namespace App\View\User\Action;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\View\RenderInterface;
use HyperfPlus\Controller\AbstractController;
use HyperfPlus\Http\Response;
use HyperfPlus\Log\StdoutLog;
use HyperfPlus\Util\Util;

class UpdateAction extends AbstractController
{
    /**
     * @Inject()
     * @var RenderInterface
     */
    private $render;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    private $rules = [
        'id' => 'required|integer',
    ];

    public function handle(RequestInterface $request, Response $response)
    {
        // 参数校验
        $requestData = $request->all();
        $this->validationFactory->make($requestData, $this->rules)->validate();
        $requestData = Util::sanitize($requestData, $this->rules);
        return $this->render->render('user/update', ['id' => $requestData['id']]);
    }
}