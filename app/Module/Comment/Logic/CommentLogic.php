<?php

namespace App\Module\Comment\Logic;

use App\Constant\AppErrorCode;
use App\Module\Article\Logic\ArticleLogic;
use App\Module\Comment\Constant\CommentConstant;
use App\Module\Comment\Service\CommentService;
use App\Util\AppException;
use App\Util\Log;
use App\Util\Util;
use Hyperf\Di\Annotation\Inject;

class CommentLogic
{
    /**
     * @Inject()
     * @var CommentService
     */
    public $service;

    /**
     * @Inject()
     * @var ArticleLogic
     */
    public $articleLogic;

    /**
     * 创建
     *
     * @param $requestData
     * @return int
     */
    public function create($requestData)
    {
        $thirdId        = $requestData['third_id'];
        $accountId      = $requestData['account_id'];
        // 评论暂时只支持文章，所以这里暂时固定为 1
        $thirdType      = CommentConstant::THIRD_TYPE_ARTICLE;
        $content        = $requestData['content'];

        // 如果是文章的评论，检查文章是否存在
        if ($thirdType === CommentConstant::THIRD_TYPE_ARTICLE) {
            $this->articleLogic->checkArticleExist($thirdId);
        }

        $createData = [
            'third_id'      => $thirdId,
            'account_id'    => $accountId,
            'third_type'    => $thirdType,
            'content'       => $content,
        ];

        return $this->service->create($createData);
    }
}