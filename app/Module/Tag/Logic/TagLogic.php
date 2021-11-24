<?php

namespace App\Module\Tag\Logic;

use App\Constant\AppErrorCode;
use App\Module\Tag\Constant\TagConstant;
use App\Util\AppException;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use App\Module\Tag\Service\TagService;
use phpDocumentor\Reflection\DocBlock\Tag;

class TagLogic
{
    /**
     * @Inject()
     * @var TagService
     */
    private $service;

    private $sort = ['sort' => 'asc', 'ctime' => 'desc'];

    /**
     * 检查 status 字段
     *
     * @param $status
     */
    public function checkStatus($status)
    {
        if (!in_array($status, TagConstant::ALLOWED_TAG_STATUS_LIST)) {
            throw new AppException(AppErrorCode::PARAMS_INVALID, 'status 参数错误！');
        }
    }

    /**
     * 创建
     *
     * @param $requestData
     * @return int
     */
    public function create($requestData)
    {
        return $this->service->create($requestData);
    }

    /**
     * 更新
     *
     * @param $requestData
     * @return int
     */
    public function update($requestData)
    {
        $id = $requestData['id'];
        unset($requestData['id']);
        return $this->service->update(['id' => $id], $requestData);
    }

    /**
     * 更新字段
     *
     * @param $requestData
     * @return int
     */
    public function updateField($requestData)
    {
        $id = $requestData['id'];
        unset($requestData['id']);

        // 检查 status 字段
        if (isset($requestData['status'])) $this->checkStatus($requestData['status']);

        return $this->service->update(['id' => $id], $requestData);
    }

    /**
     * 查找
     *
     * @param $requestData
     * @param $p
     * @param $size
     * @return array
     */
    public function search($requestData, $p, $size)
    {
        $requestData['status'] = TagConstant::TAG_STATUS_NORMAL;
        $list  = $this->service->search($requestData, $p, $size,
            ['id', 'name', 'type', 'mtime', 'ctime', 'sort'],
            ['sort' => 'asc', 'ctime' => 'desc']
        );

        $total = $this->service->count($requestData);
        return Util::formatSearchRes($p, $size, $total, $list);
    }

    /**
     * 前台标签列表
     *
     * @return array
     */
    public function list()
    {
        $requestData['status'] = TagConstant::TAG_STATUS_NORMAL;
        $list  = $this->service->search($requestData, 0, 0,
            ['id', 'name', 'sort'],
            ['sort' => 'asc', 'ctime' => 'desc']
        );
        return ['list' => $list];
    }

    /**
     * 获取一行
     *
     * @param $requestData
     * @return array
     */
    public function find($requestData)
    {
        $id = $requestData['id'];
        $tag = $this->service->getLineByWhere(['id' => $id, 'status' => TagConstant::TAG_STATUS_NORMAL]);
        if (empty($tag)) throw new AppException(AppErrorCode::TAG_NOT_EXIST_ERROR);
        return $tag;
    }
}