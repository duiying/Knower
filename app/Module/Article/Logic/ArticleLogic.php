<?php

namespace App\Module\Article\Logic;

use App\RPC\HttpRPC\ContentServiceRpc;
use Hyperf\Di\Annotation\Inject;

class ArticleLogic
{
    /**
     * @Inject()
     * @var ContentServiceRpc
     */
    private $contentServiceRpc;

    /**
     * 查找
     *
     * @param $requestData
     * @return mixed
     */
    public function search($requestData)
    {
         return $this->contentServiceRpc->searchArticle($requestData);
    }

    public function create($requestData)
    {
        return $this->contentServiceRpc->createArticle($requestData);
    }

    public function update($requestData)
    {
        return $this->contentServiceRpc->updateArticle($requestData);
    }

    public function updateField($requestData)
    {
        return $this->contentServiceRpc->updateArticleField($requestData);
    }

    public function find($requestData)
    {
        return $this->contentServiceRpc->findArticle($requestData);
    }
}