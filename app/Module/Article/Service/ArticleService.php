<?php

namespace App\Module\Article\Service;

use App\Constant\ElasticSearchConst;
use App\Module\Article\Constant\ArticleConstant;
use App\Util\ElasticSearch;
use App\Util\Log;
use Hyperf\Di\Annotation\Inject;
use App\Module\Article\Dao\ArticleDao;

class ArticleService
{
    /**
     * @Inject()
     * @var ArticleDao
     */
    private $dao;

    /**
     * @Inject()
     * @var ElasticSearch
     */
    private $es;

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        $this->dao->beginTransaction();
    }

    /**
     * 回滚事务
     */
    public function rollBack()
    {
        $this->dao->rollBack();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $this->dao->commit();
    }

    /**
     * 创建
     *
     * @param $data
     * @return int
     */
    public function create($data)
    {
        return $this->dao->create($data);
    }

    /**
     * 更新
     *
     * @param array $where
     * @param array $data
     * @return int
     */
    public function update($where = [], $data = [])
    {
        return $this->dao->update($where, $data);
    }

    /**
     * 查找
     *
     * @param array $where
     * @param int $p
     * @param int $size
     * @param string[] $columns
     * @param array $orderBy
     * @return array
     */
    public function search($where = [], $p = 0, $size = 0, $columns = ['*'], $orderBy = [])
    {
        return $this->dao->search($where, $p, $size, $columns, $orderBy);
    }

    /**
     * 获取一行
     *
     * @param array $where
     * @param string[] $columns
     * @param array $orderBy
     * @return array
     */
    public function getLineByWhere($where = [], $columns = ['*'], $orderBy = [])
    {
        return $this->dao->getLineByWhere($where, $columns, $orderBy);
    }

    /**
     * 统计
     *
     * @param array $where
     * @return int
     */
    public function count($where = [])
    {
        return $this->dao->count($where);
    }

    /**
     * 通过 ElasticSearch 搜索
     *
     * @param $params
     * @return array|callable
     */
    public function searchByEs($params)
    {
        return $this->es->esClient->search($params);
    }

    /**
     * 更新 ElasticSearch 中 Article 文档的 status 字段
     *
     * @param $id
     * @return bool
     */
    public function deleteEsArticle($id)
    {
        // 如果文档不存在，直接返回
        if (!$this->existsEsArticle($id)) return false;

        /* 如果要删除 ElasticSearch 中 Article 文档，执行下面代码
        $this->es->esClient->delete([
            'index'     => ElasticSearchConst::INDEX_ARTICLE,
            'id'        => $id,
        ]);
        */

        // 如果不删除 ElasticSearch 中 Article 文档，只更新 status 字段，执行下面代码
        $this->es->esClient->update([
            'index'     => ElasticSearchConst::INDEX_ARTICLE,
            'id'        => $id,
            'body'      => [
                'doc' => [
                    'status' => ArticleConstant::ARTICLE_STATUS_DELETE
                ],
            ],
        ]);


        return true;
    }

    /**
     * 判断 ElasticSearch 中 Article 文档是否存在
     *
     * @param $id
     * @return bool
     */
    public function existsEsArticle($id)
    {
        return $this->es->esClient->exists([
            'index'     => ElasticSearchConst::INDEX_ARTICLE,
            'id'        => $id,
        ]);
    }

    /**
     * 更新 ElasticSearch 中 Article 文档
     *
     * @param $id
     * @return bool
     */
    public function updateEsArticle($id)
    {
        $article = $this->getLineByWhere(['id' => $id, 'status' => ArticleConstant::ARTICLE_STATUS_NORMAL]);
        if (!$article) return false;

        // 如果文档不存在，首先创建文档
        if (!$this->existsEsArticle($id)) {
            return $this->createEsArticle($id);
        }

        $this->es->esClient->update([
            'index'     => ElasticSearchConst::INDEX_ARTICLE,
            'id'        => $id,
            'body'      => [
                'doc' => $article,
            ],
        ]);

        return true;
    }

    /**
     * 创建 ElasticSearch 中 Article 文档
     *
     * @param $id
     * @return bool
     */
    public function createEsArticle($id)
    {
        $article = $this->getLineByWhere(['id' => $id, 'status' => ArticleConstant::ARTICLE_STATUS_NORMAL]);
        if (!$article) return false;

        // 如果索引不存在，首先创建索引
        if (!$this->existsEsArticleIndex()) {
            $this->createEsArticleIndex();
        }

        $this->es->esClient->create([
            'index'     => ElasticSearchConst::INDEX_ARTICLE,
            'id'        => $id,
            'body'      => $article
        ]);

        return true;
    }

    /**
     * 创建 ElasticSearch 中 Article 索引
     */
    public function createEsArticleIndex()
    {
        $this->es->esClient->indices()->create([
            'index' => ElasticSearchConst::INDEX_ARTICLE,
            'body'  => [
                'settings' => ElasticSearchConst::INDEX_ARTICLE_SETTINGS,
                'mappings' => [
                    'properties' => ElasticSearchConst::INDEX_ARTICLE_MAPPINGS
                ],
            ],
        ]);
    }

    /**
     * 判断 ElasticSearch 中 Article 索引是否存在
     *
     * @return bool
     */
    public function existsEsArticleIndex()
    {
        return $this->es->esClient->indices()->exists(['index' => ElasticSearchConst::INDEX_ARTICLE]);
    }

    /**
     * 删除 ElasticSearch 中 Article 索引
     *
     * @return array
     */
    public function deleteEsArticleIndex()
    {
        return $this->es->esClient->indices()->delete(['index' => ElasticSearchConst::INDEX_ARTICLE]);
    }

    /**
     * 全量同步 ElasticSearch
     *
     * @return bool
     */
    public function asyncEs()
    {
        Log::info('MySQL 中的文章数据全量同步到 ElasticSearch begin');

        // 分页同步，防止一次性数据量过大
        $lastId = 0;
        $count  = 100;

        $index  = ElasticSearchConst::INDEX_ARTICLE;

        // 删除索引
        if ($this->existsEsArticleIndex()) {
            $this->deleteEsArticleIndex();
        }
        // 创建索引
        $this->createEsArticleIndex();

        $articleList = $this->getSyncToEsArticleData($lastId, $count);

        while (!empty($articleList)) {
            $params = ['body' => []];

            foreach ($articleList as $k => $v) {
                $params['body'][] = [
                    'index' => [
                        '_index'    => $index,
                        '_id'       => $v['id'],
                    ],
                ];
                $params['body'][] = $v;
            }

            $this->es->esClient->bulk($params);

            // 下一页数据
            $lastId         = end($articleList)['id'];
            $articleList    = $this->getSyncToEsArticleData($lastId, $count);
        }

        Log::info('MySQL 中的文章数据全量同步到 ElasticSearch end');

        return true;
    }

    /**
     * 获取需要同步到 ElasticSearch 中的文章数据
     *
     * @param int $lastId
     * @param int $count
     * @return array
     */
    public function getSyncToEsArticleData($lastId = 0, $count = 100)
    {
        return $this->search([
            'id' => ['>', $lastId]
        ], 0, $count);
    }
}