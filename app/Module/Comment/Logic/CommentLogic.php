<?php

namespace App\Module\Comment\Logic;

use App\Constant\AppErrorCode;
use App\Module\Account\Logic\AccountLogic;
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
     * @Inject()
     * @var AccountLogic
     */
    public $accountLogic;

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

        // 白名单用户，评论无需经过后台审核
        $whiteAccountIdList = [1];
        if (in_array($accountId, $whiteAccountIdList)) {
            $createData['audit'] = CommentConstant::AUDIT_AUDITED;
        }

        return $this->service->create($createData);
    }

    /**
     * 前台评论列表
     *
     * @param $requestData
     * @param $p
     * @param $size
     * @return array
     */
    public function comments($requestData, $p, $size)
    {
        // 1、先查询普通评论（而且是已审核、正常状态）
        $requestData['audit']       = CommentConstant::AUDIT_AUDITED;
        $requestData['status']      = CommentConstant::COMMENT_STATUS_NORMAL;
        $requestData['type']        = CommentConstant::TYPE_COMMENT;
        $requestData['third_type']  = CommentConstant::THIRD_TYPE_ARTICLE;

        $list  = $this->service->search($requestData, $p, $size,
            ['id', 'account_id', 'third_id', 'third_type', 'reply_id', 'comment_id', 'content', 'type', 'ctime'],
            ['ctime' => 'desc']
        );

        if (!empty($list)) {
            // 获取用户基础信息
            $accountIdList = array_column($list, 'account_id');
            $accountInfoMap = $this->accountLogic->getAccountInfoMapByIdList($accountIdList);

            // 评论列表组装信息
            foreach ($list as $k => $v) {
                // 组装用户信息
                $list[$k]['account_info'] = isset($accountInfoMap[$v['account_id']]) ? $accountInfoMap[$v['account_id']] : new \stdClass();
            }
        }

        $total = $this->service->count($requestData);
        return Util::formatSearchRes($p, $size, $total, $list);
    }
}