<?php

namespace App\Module\Comment\Logic;

use App\Constant\AppErrorCode;
use App\Module\Account\Logic\AccountLogic;
use App\Module\ActionLog\Constant\ActionLogConstant;
use App\Module\ActionLog\Logic\ActionLogLogic;
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
    private $service;

    /**
     * @Inject()
     * @var ActionLogLogic
     */
    private $actionLogLogic;

    /**
     * 检查 status 字段
     *
     * @param $status
     */
    public function checkStatus($status)
    {
        if (!in_array($status, CommentConstant::ALLOWED_STATUS_LIST)) {
            throw new AppException(AppErrorCode::PARAMS_INVALID, 'status 参数错误！');
        }
    }

    /**
     * 检查 audit 字段
     *
     * @param $audit
     */
    public function checkAudit($audit)
    {
        if (!in_array($audit, CommentConstant::ALLOWED_AUDIT_LIST)) {
            throw new AppException(AppErrorCode::PARAMS_INVALID, 'audit 参数错误！');
        }
    }

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
            $articleLogic = make(ArticleLogic::class);
            $articleLogic->checkArticleExist($thirdId);
        }

        $dayBeginTime = date('Y-m-d') . '00:00:00';
        $dayEndTime = date('Y-m-d') . '23:59:59';

        // 防止有人刷评论
        $count = $this->service->count([
            'third_id'      => $thirdId,
            'account_id'    => $accountId,
            'third_type'    => $thirdType,
            'ctime'         => ['&', [$dayBeginTime, $dayEndTime]]
        ]);
        if ($count > CommentConstant::DAY_MAX_COMMENT_NUM) {
            throw new AppException(AppErrorCode::COMMENT_TOO_MANY_ERROR);
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

        $id = $this->service->create($createData);

        // 记录操作日志
        $this->actionLogLogic->create($requestData['account_id'], $id, ActionLogConstant::TYPE_CREATE_COMMENT, $content, $requestData['client_real_ip']);

        return $id;
    }

    /**
     * 前台评论列表
     *
     * @param $requestData
     * @return array
     */
    public function comments($requestData)
    {
        $currentAccountId = $requestData['account_id'];
        unset($requestData['account_id']);

        // 1、先查询普通评论（而且是已审核、正常状态）
        $requestData['audit']       = CommentConstant::AUDIT_AUDITED;
        $requestData['status']      = CommentConstant::COMMENT_STATUS_NORMAL;
        $requestData['type']        = CommentConstant::TYPE_COMMENT;
        $requestData['third_type']  = CommentConstant::THIRD_TYPE_ARTICLE;

        // 预测评论数不会太多，这里就不做分页了
        $list  = $this->service->search($requestData, 0, 0,
            ['id', 'account_id', 'third_id', 'third_type', 'reply_id', 'comment_id', 'content', 'type', 'ctime'],
            ['ctime' => 'desc']
        );

        if (!empty($list)) {
            // 获取用户基础信息
            $accountLogic   = make(AccountLogic::class);
            $accountIdList  = array_column($list, 'account_id');
            $accountInfoMap = $accountLogic->getAccountInfoMapByIdList($accountIdList);

            // 评论列表组装信息
            foreach ($list as $k => $v) {
                $accountId = $v['account_id'];
                // 组装用户信息
                $list[$k]['account_info'] = isset($accountInfoMap[$accountId]) ? $accountInfoMap[$accountId] : new \stdClass();
                $list[$k]['format_ctime'] = Util::formatTime($v['ctime']);
                $list[$k]['show_delete'] = $currentAccountId === $accountId ? 1 : 0;
            }
        }

        $total = $this->service->count($requestData);
        return Util::formatSearchRes(0, 0, $total, $list);
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
        $list  = $this->service->search($requestData, $p, $size,
            ['*'],
            ['ctime' => 'desc']
        );

        $accountLogic = make(AccountLogic::class);
        $accountIdList = empty($list) ? [] : array_column($list, 'account_id');
        $accountInfoMap = $accountLogic->getAccountInfoMapByIdList($accountIdList);

        foreach ($list as $k => $v) {
            $accountId = $v['account_id'];
            $list[$k]['audit_text']     = CommentConstant::AUDIT_TEXT_MAP[$v['audit']];
            $list[$k]['status_text']    = CommentConstant::COMMENT_STATUS_TEXT_MAP[$v['status']];
            $list[$k]['type_text']      = CommentConstant::TYPE_TEXT_MAP[$v['type']];
            $list[$k]['account_info']   = $accountInfoMap[$accountId];
        }

        $total = $this->service->count($requestData);
        return Util::formatSearchRes($p, $size, $total, $list);
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
        if (isset($requestData['audit'])) $this->checkAudit($requestData['audit']);

        return $this->service->update(['id' => $id], $requestData);
    }

    /**
     * 获取评论数
     *
     * @param $thirdIdList
     * @param $thirdType
     * @return array
     */
    public function getCommentCountMap($thirdIdList, $thirdType)
    {
        $thirdIdList = array_unique($thirdIdList);
        $commentCountList = $this->service->getCommentCount($thirdIdList, $thirdType);
        $map = [];
        foreach ($commentCountList as $k => $v) {
            $map[$v['third_id']] = $v['count'];
        }
        return $map;
    }

    /**
     * 删除评论
     *
     * @param $requestData
     * @return int
     */
    public function deleteComment($requestData)
    {
        $id = $requestData['id'];
        $accountId = $requestData['account_id'];

        $where = [
            'id'            => $id,
            'status'        => CommentConstant::COMMENT_STATUS_NORMAL,
            'account_id'    => $accountId
        ];

        // 先检查评论是否存在
        $comment = $this->service->getLineByWhere($where, ['id', 'content']);
        if (empty($comment)) throw new AppException(AppErrorCode::COMMENT_NOT_EXIST_ERROR);

        // 如果评论存在，更新状态为已删除
        $res = $this->service->update($where, ['status' => CommentConstant::COMMENT_STATUS_DELETE]);

        // 记录操作日志
        $this->actionLogLogic->create($accountId, $id, ActionLogConstant::TYPE_DELETE_COMMENT, $comment['content'], $requestData['client_real_ip']);

        return $res;
    }

    /**
     * 评论总数量
     *
     * @return int
     */
    public function count()
    {
        return $this->service->count(['status' => CommentConstant::COMMENT_STATUS_NORMAL]);
    }
}