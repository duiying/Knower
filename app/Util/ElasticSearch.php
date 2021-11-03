<?php

namespace App\Util;

use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\Utils\ApplicationContext;

class ElasticSearch
{
    public $esClient;

    public function __construct()
    {
        $builder = ApplicationContext::getContainer()->get(ClientBuilderFactory::class)->create();
        $this->esClient = $builder->setHosts(config('databases.elasticsearch.hosts'))->build();
    }
}