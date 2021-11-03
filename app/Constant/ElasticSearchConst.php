<?php

namespace App\Constant;

/**
 * ElasticSearch 常量类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Constant
 */
class ElasticSearchConst
{
    // 文章索引
    const INDEX_ARTICLE = 'article';

    // 文章索引设置
    const INDEX_ARTICLE_SETTINGS = [
        // 主分片数量
        'number_of_shards'      => 1,
        // 副本分片数量
        'number_of_replicas'    => 0,
    ];

    // 文章索引映射
    const INDEX_ARTICLE_MAPPINGS = [
        'id'        => ['type' => 'integer'],
        'title'     => ['type' => 'text', 'analyzer' => 'ik_smart'],
        'content'   => ['type' => 'text', 'analyzer' => 'ik_smart'],
        'status'    => ['type' => 'byte'],
        'sort'      => ['type' => 'integer'],
        'mtime'     => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
        'ctime'     => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
    ];
}