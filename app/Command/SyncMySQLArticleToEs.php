<?php

namespace App\Command;

use App\Constant\ElasticSearchConst;
use App\Module\Article\Logic\ArticleLogic;
use App\Module\Article\Service\ArticleService;
use App\Util\ElasticSearch;
use Hyperf\Di\Annotation\Inject;
use App\Util\Log;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;

/**
 * MySQL 中的文章数据全量同步到 ElasticSearch
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Command
 * @Command
 */
class SyncMySQLArticleToEs extends HyperfCommand
{
    /**
     * @Inject()
     * @var ElasticSearch
     */
    private $es;

    /**
     * @Inject()
     * @var ArticleLogic
     */
    private $articleLogic;

    /**
     * @Inject()
     * @var ArticleService
     */
    private $articleService;

    /**
     * 执行的命令行（php bin/hyperf.php sync:mysql:article:to:es）
     *
     * @var string
     */
    protected $name = 'sync:mysql:article:to:es';

    public function configure()
    {
        parent::configure();
        $this->setDescription('MySQL 中的文章数据全量同步到 ElasticSearch');
    }

    public function handle()
    {
        Log::info('MySQL 中的文章数据全量同步到 ElasticSearch begin');

        // 分页同步，防止一次性数据量过大
        $lastId = 0;
        $count  = 100;

        $index  = ElasticSearchConst::INDEX_ARTICLE;

        // 删除索引
        if ($this->articleService->existsEsArticleIndex()) {
            $this->articleService->deleteEsArticleIndex();
        }
        // 创建索引
        $this->articleService->createEsArticleIndex();

        $articleList = $this->articleLogic->getSyncToEsArticleData($lastId, $count);

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
            $articleList    = $this->articleLogic->getSyncToEsArticleData($lastId, $count);
        }

        Log::info('MySQL 中的文章数据全量同步到 ElasticSearch end');
    }
}