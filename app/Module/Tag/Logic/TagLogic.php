<?php

namespace App\Module\Tag\Logic;

use App\Constant\AppErrorCode;
use App\Module\Tag\Constant\TagConstant;
use App\Module\Tag\Constant\TagRelationConstant;
use App\Module\Tag\Service\TagRelationService;
use App\Util\AppException;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use App\Module\Tag\Service\TagService;

class TagLogic
{
    /**
     * @Inject()
     * @var TagService
     */
    private $service;

    /**
     * @Inject()
     * @var TagRelationService
     */
    private $tagRelationService;

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

    /**
     * 创建或更新标签关联记录
     *
     * @param $thirdId
     * @param $type
     * @param array $tagIdList
     */
    public function createOrUpdateRelation($thirdId, $type, $tagIdList = [])
    {
        $tagIdList = array_unique($tagIdList);
        foreach ($tagIdList as $k => $v) {
            $tagIdList[$k] = intval($v);
        }

        // 先把以前拥有的标签置成删除状态
        $this->tagRelationService->update([
            'third_id'  => $thirdId,
            'type'      => $type,
        ], [
            'status'    => TagRelationConstant::TAG_RELATION_STATUS_DELETE
        ]);

        // 如果没有记录，会创建一条记录；如果有记录，会改成正常状态；
        foreach ($tagIdList as $k => $v) {
            $tagRelation = $this->tagRelationService->getLineByWhere([
                'third_id'  => $thirdId,
                'type'      => $type,
                'tag_id'    => $v
            ]);
            if (empty($tagRelation)) {
                $this->tagRelationService->create([
                    'third_id'  => $thirdId,
                    'type'      => $type,
                    'tag_id'    => $v
                ]);
            } else {
                $id = $tagRelation['id'];
                $this->tagRelationService->update(['id' => $id], ['status' => TagRelationConstant::TAG_RELATION_STATUS_NORMAL]);
            }
        }
    }

    /**
     * 获取拥有的标签列表
     *
     * @param $thirdIdList
     * @param $type
     * @return array
     */
    public function getTagList($thirdIdList, $type)
    {
        $tagInfoList = $this->tagRelationService->getTagList($thirdIdList, $type);
        $map = [];
        foreach ($tagInfoList as $k => $v) {
            $map[$v['third_id']][] = [
                'id' => $v['tag_id'],
                'name' => $v['name'],
            ];
        }
        return $map;
    }

    /**
     * 根据标签 ID 获取关联的第三方 ID
     *
     * @param $tagId
     * @return array
     */
    public function getThirdIdListByTagId($tagId)
    {
        $list = $this->tagRelationService->getThirdIdListByTagId($tagId);
        $thirdIdList = empty($list) ? [] : array_column($list, 'third_id');
        foreach ($thirdIdList as $k => $v) {
            $thirdIdList[$k] = intval($v);
        }
        return $thirdIdList;
    }
}