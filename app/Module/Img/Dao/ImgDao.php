<?php

namespace App\Module\Img\Dao;

use App\Module\Img\Constant\ImgConstant;
use App\Util\MySQLDao;
use App\Util\Util;
use Hyperf\DbConnection\Db;

class ImgDao extends MySQLDao
{
    public $connection = 'content';
    public $table = 't_content_img';

    /**
     * 根据远程图片 url 获取本地图片 url
     *
     * @param array $originUrlList
     * @return array
     */
    public function getImgLocalUrlByOriginUrl($originUrlList = [])
    {
        $originUrlList = array_unique($originUrlList);
        if (empty($originUrlList)) {
            return [];
        }

        $inStr = "";
        foreach ($originUrlList as $k => $v) {
            $inStr .= "'{$v}',";
        }
        $inStr = substr_replace($inStr, '', -1);
        $sql = "select id, origin_url, local_url from {$this->table} where origin_url in ({$inStr}) and status = " . ImgConstant::IMG_STATUS_NORMAL;

        $list = Db::connection($this->connection)->select($sql);

        return Util::objArr2Arr($list);
    }
}