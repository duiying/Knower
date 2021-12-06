<?php

namespace App\Command;

use App\Module\Article\Service\ArticleService;
use Hyperf\Di\Annotation\Inject;
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
        $this->articleService->asyncEs();
    }
}