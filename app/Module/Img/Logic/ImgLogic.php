<?php

namespace App\Module\Img\Logic;

use App\Constant\AppErrorCode;
use App\Module\Img\Constant\ImgConstant;
use App\Util\AppException;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use App\Module\Img\Service\ImgService;

class ImgLogic
{
    /**
     * @Inject()
     * @var ImgService
     */
    private $service;

    /**
     * 根据图片原 url，返回图片 id
     *
     * @param string $originUrl
     * @return int
     */
    public function findOrCreateImgByOriginUrl($originUrl = '')
    {
        if (empty($originUrl)) return 0;

        // 1、先检查该图片 url 是否已在表中，如果存在，直接返回图片 id
        $imgInfo = $this->service->getLineByWhere(['origin_url' => $originUrl, 'status' => ImgConstant::IMG_STATUS_NORMAL]);

        if (!empty($imgInfo)) {
            return intval($imgInfo['id']);
        }

        // 2、如果图片 url 不在表中，新建一条记录
        $createImgParams = [
            'origin_url'    => $originUrl,
            'local_url'     => '',
        ];

        return $this->service->create($createImgParams);
    }

    /**
     * 根据图片 id 获取图片 url
     *
     * @param array $imgIdList
     * @return array
     */
    public function getImgUrlMapByIdList($imgIdList = [])
    {
        if (empty($imgIdList)) return [];

        $imgIdList = array_filter(array_unique($imgIdList));

        $imgInfoList = $this->service->search(['id' => $imgIdList, 'status' => ImgConstant::IMG_STATUS_NORMAL]);

        $imgUrlMap = [];

        foreach ($imgInfoList as $k => $v) {
            $imgUrlMap[$v['id']] = $v['origin_url'];
        }

        return $imgUrlMap;
    }
}