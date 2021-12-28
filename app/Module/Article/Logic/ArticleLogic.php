<?php

namespace App\Module\Article\Logic;

use App\Constant\AppErrorCode;
use App\Constant\CommonConstant;
use App\Constant\ElasticSearchConst;
use App\Constant\RedisKeyConst;
use App\Module\ActionLog\Constant\ActionLogConstant;
use App\Module\ActionLog\Logic\ActionLogLogic;
use App\Module\Article\Constant\ArticleConstant;
use App\Module\Comment\Constant\CommentConstant;
use App\Module\Comment\Logic\CommentLogic;
use App\Module\Img\Logic\ImgLogic;
use App\Module\Tag\Constant\TagConstant;
use App\Module\Tag\Logic\TagLogic;
use App\Util\AppException;
use App\Util\Log;
use App\Util\Redis;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;
use App\Module\Article\Service\ArticleService;

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

        $createParams = [
            'title'     => $requestData['title'],
            'desc'      => $requestData['desc'],
            'content'   => $requestData['content'],
        ];
        if (isset($requestData['sort'])) $createParams['sort'] = $requestData['sort'];
        if (isset($requestData['cover_img_id'])) $createParams['cover_img_id'] = $requestData['cover_img_id'];

        $id = $this->service->create($createParams);

        // 写入 ElasticSearch
        try {
            $this->service->createEsArticle($id);
        } catch (\Exception $exception) {
            Log::error('创建 ElasticSearch 中 Article 文档异常', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'id' => $id]);
        }

        // 下载图片到本地
        $this->downloadImg4Content($requestData['content']);

        // 更新文章的标签
        $tagIdList = [];
        if (isset($requestData['tag_ids']) && !empty($requestData['tag_ids'])) {
            $tagIdList = explode(',', $requestData['tag_ids']);
        }
        $this->updateArticleTag($id, $tagIdList);

        return $id;
    }

    /**
     * 更新文章的标签
     *
     * @param $id
     * @param array $tagIdList
     */
    public function updateArticleTag($id, $tagIdList = [])
    {
        $tagLogic = make(TagLogic::class);
        $tagLogic->createOrUpdateRelation($id, TagConstant::TAG_TYPE_ARTICLE, $tagIdList);
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

        $updateParams = [
            'title'     => $requestData['title'],
            'desc'      => $requestData['desc'],
            'content'   => $requestData['content'],
        ];
        if (isset($requestData['sort'])) $updateParams['sort'] = $requestData['sort'];
        if (isset($requestData['cover_img_id'])) $updateParams['cover_img_id'] = $requestData['cover_img_id'];

        // 1、先检查文章是否存在
        $this->checkArticleExist($id);

        // 2、检查封面图片 ID 是否存在
        if (isset($requestData['cover_img_id']) && !empty($requestData['cover_img_id'])) {
            $this->imgLogic->checkImgExist($requestData['cover_img_id']);
        }

        // 3、更新 MySQL
        $updateRes = $this->service->update(['id' => $id], $updateParams);

        // 4、更新 ElasticSearch
        try {
            $this->service->updateEsArticle($id);
        } catch (\Exception $exception) {
            Log::error('更新 ElasticSearch 中 Article 文档异常', ['code' => $exception->getCode(), 'msg' => $exception->getMessage(), 'id' => $id]);
        }

        // 5、下载图片到本地
        $this->downloadImg4Content($requestData['content']);

        // 6、更新文章的标签
        $tagIdList = [];
        if (isset($requestData['tag_ids']) && !empty($requestData['tag_ids'])) {
            $tagIdList = explode(',', $requestData['tag_ids']);
        }
        $this->updateArticleTag($id, $tagIdList);

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

        $this->service->asyncEs();

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
     * @param false $fromFrontend
     * @return array
     */
    public function search($requestData, $p, $size, $fromFrontend = false)
    {
        if ($fromFrontend) {
            // 前台只展示正常状态的文章
            $requestData['status'] = ArticleConstant::ARTICLE_STATUS_NORMAL;
        }

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
        if (empty($list)) return;

        $idList = array_column($list, 'id');

        // 封面图片
        $coverImgIdList = array_column($list, 'cover_img_id');
        $imgInfoMap = $this->imgLogic->getImgUrlMapByIdList($coverImgIdList);

        // 评论数
        $commentLogic = make(CommentLogic::class);
        $commentCountMap = $commentLogic->getCommentCountMap($idList, CommentConstant::THIRD_TYPE_ARTICLE);

        foreach ($list as $k => $v) {
            $list[$k]['status_text']        = ArticleConstant::ARTICLE_STATUS_TEXT_MAP[$v['status']];
            $list[$k]['cover_img_url']      = isset($imgInfoMap[$v['cover_img_id']]) ? $imgInfoMap[$v['cover_img_id']] : '';
            $list[$k]['comment_count']      = isset($commentCountMap[$v['id']]) ? $commentCountMap[$v['id']] : 0;
            unset($list[$k]['cover_img_id']);
        }
    }

    /**
     * 获取一行
     *
     * @param $requestData
     * @param false $fromFrontend
     * @return array
     */
    public function find($requestData, $fromFrontend = false)
    {
        $id = $requestData['id'];
        $this->checkArticleExist($id);
        $where = [
            'id' => $id
        ];
        $article = $this->service->getLineByWhere($where);

        // 查出文章的封面图片信息
        $coverImgId = $article['cover_img_id'];
        if ($coverImgId) {
            $imgInfoMap = $this->imgLogic->getImgUrlMapByIdList([$coverImgId]);
        }
        $article['cover_img_url'] = $coverImgId && isset($imgInfoMap[$coverImgId]) ? $imgInfoMap[$coverImgId] : '';
        $article['filename'] = empty($article['cover_img_url']) ? '' : basename($article['cover_img_url']);

        // 查出文章的标签信息
        $tagLogic = make(TagLogic::class);
        $tagMap = $tagLogic->getTagList([$id], TagConstant::TAG_TYPE_ARTICLE);
        $article['tag_list'] = isset($tagMap[$id]) ? $tagMap[$id] : [];

        if ($fromFrontend) {
            $article['cached_content'] = '';
            if (CommonConstant::MARKDOWN_IMG_CACHE) {
                $article['cached_content'] = $this->replaceOriginImg2Local($article['content']);
            }

            // 阅读数 +1
            $this->service->incrReadCount($id);

            // 记录操作日志
            $actionLogLogic = make(ActionLogLogic::class);
            $actionLogLogic->create($requestData['account_id'], $id, ActionLogConstant::TYPE_ARTICLE_DETAIL, $article['title'], $requestData['client_real_ip']);
        }

        return $article;
    }

    /**
     * 替换内容中的远程图片 url 为本地图片 url
     *
     * @param string $content
     * @return string
     */
    public function replaceOriginImg2Local($content = '')
    {
        if (empty($content)) {
            return '';
        }

        // 1、匹配原生 HTML img 标签语法；2、匹配 markdown img 语法；
        $pregHtmlImg        = "/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i";
        $pregMarkdownImg    = '/!\\[.*\\]\\((.+)\\)/';

        preg_match_all($pregHtmlImg, $content, $pregHtmlImgInfoList);
        preg_match_all($pregMarkdownImg, $content, $pregMarkdownImgInfoList);

        // 需要替换的图片 url 列表
        $originImgUrlList = [];

        if (isset($pregHtmlImgInfoList[1]) && !empty($pregHtmlImgInfoList[1])) {
            $originImgUrlList = array_merge($originImgUrlList, $pregHtmlImgInfoList[1]);
        }

        if (isset($pregMarkdownImgInfoList[1]) && !empty($pregMarkdownImgInfoList[1])) {
            $originImgUrlList = array_merge($originImgUrlList, $pregMarkdownImgInfoList[1]);
        }

        // Markdown 文本中没有要替换的图片
        if (empty($originImgUrlList)) return $content;

        $urlMap = $this->imgLogic->getImgLocalUrlByOriginUrl($originImgUrlList);

        // Markdown 中有需要替换的图片 url，但是在图片表中没有查到本地的 url
        if (empty($urlMap)) return $content;

        foreach ($urlMap as $originUrl => $localUrl) {
            if (!empty($localUrl)) {
                $content = str_replace($originUrl, $localUrl, $content);
            }
        }

        return $content;
    }

    /**
     * 正则匹配文章内容中的远程图片，下载到本地
     * 为什么需要这个方法呢？因为我用了 GitHub 作为图床，图片经常加载不出来，于是将图片缓存在本地吧
     *
     * @param string $content
     */
    public function downloadImg4Content($content = '')
    {
        Log::info('异步下载内容中的图片开始');

        if (empty($content)) {
            return;
        }

        // 1、匹配原生 HTML img 标签语法；2、匹配 markdown img 语法；
        $pregHtmlImg        = "/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i";
        $pregMarkdownImg    = '/!\\[.*\\]\\((.+)\\)/';

        preg_match_all($pregHtmlImg, $content, $pregHtmlImgInfoList);
        preg_match_all($pregMarkdownImg, $content, $pregMarkdownImgInfoList);

        if (isset($pregHtmlImgInfoList[1]) && !empty($pregHtmlImgInfoList[1])) {
            foreach ($pregHtmlImgInfoList[1] as $k => $v) {
                $this->imgLogic->download($v);
            }
        }

        if (isset($pregMarkdownImgInfoList[1]) && !empty($pregMarkdownImgInfoList[1])) {
            foreach ($pregMarkdownImgInfoList[1] as $k => $v) {
                $this->imgLogic->download($v);
            }
        }

        Log::info('异步下载内容中的图片结束');
    }
}