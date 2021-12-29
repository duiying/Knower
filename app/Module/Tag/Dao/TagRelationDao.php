<?php

namespace App\Module\Tag\Dao;

use App\Module\Tag\Constant\TagConstant;
use App\Module\Tag\Constant\TagRelationConstant;
use App\Util\MySQLDao;
use App\Util\Util;
use Hyperf\DbConnection\Db;

class TagRelationDao extends MySQLDao
{
    public $connection = 'content';
    public $table = 't_content_tag_relation';

    /**
     * 获取拥有的标签列表
     *
     * @param $thirdIdList
     * @param $type
     * @return array
     */
    public function getTagList($thirdIdList, $type)
    {
        $thirdIdList = array_unique($thirdIdList);
        if (empty($thirdIdList)) return [];

        $thirdIdStr = implode(',', $thirdIdList);
        $tagStatus = TagConstant::TAG_STATUS_NORMAL;
        $tagRelationStatus = TagRelationConstant::TAG_RELATION_STATUS_NORMAL;

        $sql = "select r.third_id, r.tag_id, t.name from {$this->table} as r left join t_content_tag as t on r.tag_id = t.id 
where r.third_id in ($thirdIdStr) and r.type = {$type} and t.status = {$tagStatus} and r.status = {$tagRelationStatus}";

        $list = Db::connection($this->connection)->select($sql);

        return Util::objArr2Arr($list);
    }

    /**
     * 根据标签 ID 获取关联的第三方 ID
     *
     * @param $tagId
     * @return array
     */
    public function getThirdIdListByTagId($tagId)
    {
        $tagStatus = TagConstant::TAG_STATUS_NORMAL;
        $tagRelationStatus = TagRelationConstant::TAG_RELATION_STATUS_NORMAL;

        $sql = "select r.third_id from {$this->table} as r left join t_content_tag as t on r.tag_id = t.id 
where r.tag_id = {$tagId} and t.status = {$tagStatus} and r.status = {$tagRelationStatus}";

        $list = Db::connection($this->connection)->select($sql);

        return Util::objArr2Arr($list);
    }
}