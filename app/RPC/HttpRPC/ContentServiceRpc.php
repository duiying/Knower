<?php

namespace App\RPC\HttpRPC;

use Hyperf\Di\Annotation\Inject;
use HyperfPlus\Http\Client;
use HyperfPlus\RPC\HttpRPC;
use HyperfPlus\Constant\Constant;

class ContentServiceRpc extends HttpRPC
{
    public $service;

    /**
     * @Inject()
     * @var Client
     */
    public $client;

    public function __construct()
    {
        $this->service = env('CONTENT_SERVICE_URI');
    }

    public function searchArticle($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/article/search',
        ]);
    }

    public function createArticle($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/article/create',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function updateArticle($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/article/update',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function updateArticleField($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/article/update_field',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function findArticle($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/article/find',
        ]);
    }
}