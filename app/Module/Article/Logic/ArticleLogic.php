<?php

namespace App\Module\Article\Logic;

use App\Constant\AppErrorCode;
use App\Constant\ElasticSearchConst;
use App\Constant\RedisKeyConst;
use App\Module\Article\Constant\ArticleConstant;
use App\Module\Img\Logic\ImgLogic;
use App\Util\AppException;
use App\Util\HttpUtil;
use App\Util\Log;
use App\Util\Redis;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use App\Module\Article\Service\ArticleService;
use Hyperf\Task\Task;
use Hyperf\Task\TaskExecutor;
use Hyperf\Utils\ApplicationContext;

class ArticleLogic
{
    /**
     * @Inject()
     * @var ArticleService
     */
    private $service;

    /**
     * @Inject()
     * @var ImgLogic
     */
    private $imgLogic;

    private $sort = ['sort' => 'asc', 'ctime' => 'desc'];

    /**
     * 检查 status 字段
     *
     * @param $status
     */
    public function checkStatus($status)
    {
        if (!in_array($status, ArticleConstant::ALLOWED_ARTICLE_STATUS_LIST)) {
            throw new AppException(AppErrorCode::PARAMS_INVALID, 'status 参数错误！');
        }
    }

    /**
     * 检查文章是否存在
     *
     * @param $id
     * @return bool
     */
    public function checkArticleExist($id)
    {
        $article = $this->service->getLineByWhere(['id' => $id, 'status' => ArticleConstant::ARTICLE_STATUS_NORMAL], ['id']);
        if (empty($article)) {
            throw new AppException(AppErrorCode::ARTICLE_NOT_EXIST_ERROR);
        }
        return true;
    }

    /**
     * 创建
     *
     * @param $requestData
     * @return int
     */
    public function create($requestData)
    {
        if (isset($requestData['cover_img_id']) && !empty($requestData['cover_img_id'])) {
            $this->imgLogic->checkImgExist($requestData['cover_img_id']);
        }

        $id = $this->service->create($requestData);

        // 写入 ElasticSearch
        try {
            $this->service->createEsArticle($id);
        } catch (\Exception $exception) {
            Log::error('创建 ElasticSearch 中 Article 文档异常', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'id' => $id]);
        }

        return $id;
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

        // 1、先检查文章是否存在
        $this->checkArticleExist($id);

        // 2、检查封面图片 ID 是否存在
        if (isset($requestData['cover_img_id']) && !empty($requestData['cover_img_id'])) {
            $this->imgLogic->checkImgExist($requestData['cover_img_id']);
        }

        // 3、更新 MySQL
        $updateRes = $this->service->update(['id' => $id], $requestData);

        // 4、更新 ElasticSearch
        try {
            $this->service->updateEsArticle($id);
        } catch (\Exception $exception) {
            Log::error('更新 ElasticSearch 中 Article 文档异常', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'id' => $id]);
        }

        return $updateRes;
    }

    /**
     * 全量同步 ElasticSearch
     *
     * @return bool
     * @throws \Throwable
     */
    public function asyncEs()
    {
        // 限流
        $redis = Redis::instance();
        $noRepeat = $redis->set(RedisKeyConst::ASYNC_ES, 1, ['nx', 'ex' => 10]);
        if (!$noRepeat) {
            throw new AppException(AppErrorCode::ACTION_TOO_FAST);
        }

        // 投递 Task（全量同步 ElasticSearch 是耗时任务，使用 Task 进程来防止接口超时）
        $container = ApplicationContext::getContainer();
        $exec = $container->get(TaskExecutor::class);
        try {
            $result = $exec->execute(new Task([ArticleService::class, 'asyncEs']));
        } catch (\Exception $exception) {
            Log::error('全量同步 ElasticSearch 异常', ['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
        }

        // 投递 Task 之后，直接返回 true
        return true;
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

        if ($requestData['status'] == ArticleConstant::ARTICLE_STATUS_DELETE) {
            try {
                $this->service->deleteEsArticle($id);
            } catch (\Exception $exception) {
                Log::error('删除 ElasticSearch 中 Article 文档异常', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'id' => $id]);
            }
        }

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
        if (isset($requestData['keywords'])) {
            // 通过 ElasticSearch 查询
            return $this->searchByEs($requestData, $p, $size);
        }
        // 走 MySQL 查询
        return $this->searchByMySQL($requestData, $p, $size);
    }

    /**
     * 通过 MySQL 查询
     *
     * @param $requestData
     * @param $p
     * @param $size
     * @return array
     */
    public function searchByMySQL($requestData, $p, $size)
    {
        $list  = $this->service->search($requestData, $p, $size, ['*'], ['sort' => 'asc', 'ctime' => 'desc']);
        $total = $this->service->count($requestData);
        foreach ($list as $k => $v) {
            $list[$k]['highlight_title']    = '';
            $list[$k]['highlight_desc']     = '';
            $list[$k]['highlight_content']  = '';
        }
        $this->assembleArticleList($list);
        return Util::formatSearchRes($p, $size, $total, $list);
    }

    /**
     * 通过 ElasticSearch 查询
     *
     * @param $requestData
     * @param $p
     * @param $size
     * @return array
     */
    public function searchByEs($requestData, $p, $size)
    {
        // 如果索引不存在，返回空列表
        if (!$this->service->existsEsArticleIndex()) {
            return Util::formatSearchRes($p, $size, 0, []);
        }

        $params = [
            'index' => ElasticSearchConst::INDEX_ARTICLE,
            'body'  => [
                'query' => [
                    'bool' => [
                        'filter'    => [],
                        'must'      => [],
                    ]
                ],
                'highlight' => [
                    'require_field_match'   => false,
                    'fields'                => ['title' => new \stdClass(), 'desc' => new \stdClass(), 'content' => new \stdClass()],
                    'pre_tags'              => ["<code>"],
                    'post_tags'             => ["</code>"],
                ],
                'sort' => $this->sort
            ],
            'from' => ($p - 1) * $size,
            'size' => $size,
        ];

        if (isset($requestData['keywords']) && !empty($requestData['keywords'])) {
            $keywordList = array_filter(explode(' ', $requestData['keywords']));

            foreach ($keywordList as $k => $v) {
                $params['body']['query']['bool']['must'][] = [
                    'multi_match' => [
                        'query'     => $v,
                        'fields'    => ['title', 'desc', 'content'],
                    ]
                ];
            }
        }

        if (isset($requestData['status'])) {
            $params['body']['query']['bool']['filter'][] = ['term' => ['status' => $requestData['status']]];
        }

        $elasticSearchRes = $this->service->searchByEs($params);

        $total  = isset($elasticSearchRes['hits']['total']['value']) ? $elasticSearchRes['hits']['total']['value'] : 0;
        $list   = [];

        if (isset($elasticSearchRes['hits']['hits'])) {
            foreach ($elasticSearchRes['hits']['hits'] as $k => $v) {
                $tmpArticle                         = $v['_source'];
                $tmpArticle['title']                = strip_tags($tmpArticle['title']);
                $tmpArticle['desc']                 = strip_tags($tmpArticle['desc']);
                $tmpArticle['content']              = strip_tags($tmpArticle['content']);

                // 高亮逻辑
                $tmpArticle['highlight_content']    = '';
                $tmpArticle['highlight_title']      = '';
                $tmpArticle['highlight_desc']       = '';
                if (isset($v['highlight']['content']) && !empty($v['highlight']['content'])) {
                    $tmpArticle['highlight_content'] = strip_tags($v['highlight']['content'][0], '<code>');
                }
                if (isset($v['highlight']['title']) && !empty($v['highlight']['title'])) {
                    $tmpArticle['highlight_title'] = strip_tags($v['highlight']['title'][0], '<code>');
                }
                if (isset($v['highlight']['desc']) && !empty($v['highlight']['desc'])) {
                    $tmpArticle['highlight_desc'] = strip_tags($v['highlight']['desc'][0], '<code>');
                }

                $list[] = $tmpArticle;
            }
        }

        $this->assembleArticleList($list);

        return Util::formatSearchRes($p, $size, $total, $list);
    }

    /**
     * 组装后台列表字段
     *
     * @param $list
     */
    public function assembleArticleList(&$list)
    {
        if (empty($list)) {
            return;
        }
        $coverImgIdList = empty($list) ? [] : array_column($list, 'cover_img_id');
        $imgInfoMap = $this->imgLogic->getImgUrlMapByIdList($coverImgIdList);
        foreach ($list as $k => $v) {
            $list[$k]['status_text']        = ArticleConstant::ARTICLE_STATUS_TEXT_MAP[$v['status']];
            $list[$k]['cover_img_url']      = isset($imgInfoMap[$v['cover_img_id']]) ? $imgInfoMap[$v['cover_img_id']] : '';
            unset($list[$k]['cover_img_id']);
        }
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
        $this->checkArticleExist($id);
        return $this->service->getLineByWhere($requestData);
    }
}