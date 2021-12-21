<?php

namespace App\Module\Img\Action;

use App\Constant\AppErrorCode;
use App\Module\Img\Logic\ImgLogic;
use App\Util\HttpUtil;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class UploadAction
{
    /**
     * @Inject()
     * @var ImgLogic
     */
    private $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    public function handle(RequestInterface $request, ResponseInterface $response, \League\Flysystem\Filesystem $filesystem)
    {
        $file       = $request->file('file');
        if (!$file) {
            return HttpUtil::error($response, AppErrorCode::PARAMS_INVALID, '上传图片不能为空！');
        }
        $stream     = fopen($file->getRealPath(), 'r+');
        $filename   = $file->getClientFilename();

        // 如果有该文件名，需要进行重命名
        if (file_exists(BASE_PATH . '/public/' . $filename)) {
            $filename = date('YmdHis') . rand(1000, 9999) . '.' . explode('.', $file->getClientFilename())[1];
        }

        // 保存文件到本地
        $filesystem->writeStream($filename, $stream);

        // 插入一条新记录
        $imgUrl = '/public/' . $filename;
        $id = $this->logic->create($imgUrl, $imgUrl);

        return HttpUtil::success($response, [
            'id'        => $id,
            'cover_img' => $imgUrl
        ]);
    }
}