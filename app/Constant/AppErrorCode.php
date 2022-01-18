<?php

namespace App\Constant;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * 应用错误码类
 *
 * @author Yaxian <wangyaxiandev@gmail.com>
 * @package App\Constant
 */

/**
 * @Constants
 */
class AppErrorCode extends AbstractConstants
{
    /******************** 基础错误 begin 1001 ~ 1200 ********************************************************************/
    /**
     * @Message("参数错误！")
     */
    const PARAMS_INVALID                                = 1001;

    /**
     * @Message("服务异常！")
     */
    const TRIGGER_EXCEPTION                             = 1002;

    /**
     * @Message("请勿重复操作！")
     */
    const REPEAT_EXCEPTION                              = 1003;

    /**
     * @Message("字段不能为空！")
     */
    const FIELD_EMPTY_EXCEPTION                         = 1004;

    /**
     * @Message("操作过快！")
     */
    const ACTION_TOO_FAST                               = 1005;

    /******************** 基础错误 end **********************************************************************************/

    /******************** 角色模块错误码 begin 3000001 ~ 3000100 *********************************************************/

    /**
     * @Message("角色不存在！")
     */
    const ROLE_NOT_EXIST_ERROR = 3000001;

    /**
     * @Message("超级管理员不允许修改！")
     */
    const ROLE_ADMIN_UPDATE_ERROR = 3000002;

    /**
     * @Message("角色名称已存在！")
     */
    const ROLE_NAME_REPEAT_ERROR = 3000003;

    /******************** 角色模块错误码 end *****************************************************************************/

    /******************** 权限模块错误码 begin 3000101 ~ 3000200 *********************************************************/

    /**
     * @Message("权限不存在！")
     */
    const PERMISSION_NOT_EXIST_ERROR = 3000101;

    /******************** 权限模块错误码 end *****************************************************************************/

    /******************** 管理员模块错误码 begin 3000201 ~ 3000300 *******************************************************/

    /**
     * @Message("管理员不存在！")
     */
    const USER_NOT_EXIST_ERROR = 3000201;

    /**
     * @Message("ROOT 管理员不允许删除！")
     */
    const ROOT_USER_DELETE_ERROR = 3000202;

    /**
     * @Message("密码输入错误！")
     */
    const USER_PASSWORD_ERROR = 3000203;

    /**
     * @Message("登录令牌失效，请重新登录！")
     */
    const TOKEN_INVALID_ERROR = 3000204;

    /**
     * @Message("邮箱已存在！")
     */
    const EMAIL_REPEAT_ERROR = 3000205;

    /**
     * @Message("无任何权限！")
     */
    const USER_ROLE_EMPTY_ERROR = 3000206;

    /**
     * @Message("无任何权限！")
     */
    const USER_ROLE_PERMISSION_EMPTY_ERROR = 3000207;

    /**
     * @Message("无权限！")
     */
    const USER_PERMISSION_ERROR = 3000208;

    /******************** 管理员模块错误码 end ***************************************************************************/



    /******************** 菜单模块错误码 begin 3000301 ~ 3000400 *********************************************************/

    /**
     * @Message("角色不存在！")
     */
    const MENU_NOT_EXIST_ERROR = 3000301;

    /**
     * @Message("路由不能为空！")
     */
    const MENU_URL_EMPTY_ERROR = 3000302;

    /**
     * @Message("请先删除下面的二级菜单！")
     */
    const CLASS_A_MENU_DELETE_ERROR = 3000303;

    /******************** 菜单模块错误码 end *****************************************************************************/

    /******************** 标签模块错误码 begin 3000401 ~ 3000500 *********************************************************/

    /**
     * @Message("标签不存在！")
     */
    const TAG_NOT_EXIST_ERROR = 3000401;

    /******************** 标签模块错误码 end *****************************************************************************/

    /******************** 用户模块错误码 begin 3000501 ~ 3000600 *********************************************************/

    /**
     * @Message("获取 GitHub access_token 异常！")
     */
    const GITHUB_ACCESS_TOKEN_FAIL = 3000501;

    /**
     * @Message("GitHub 返回 access_token 信息异常！")
     */
    const GITHUB_TOKEN_INFO_ERROR = 3000502;

    /**
     * @Message("获取 GitHub 用户信息失败！")
     */
    const GITHUB_GET_USER_INFO_FAIL = 3000503;

    /**
     * @Message("GitHub 返回用户信息异常！")
     */
    const GITHUB_USER_INFO_ERROR = 3000504;

    /**
     * @Message("用户注册失败！")
     */
    const USER_REGISTER_FAIL = 3000505;

    /**
     * @Message("用户登录信息不存在！")
     */
    const USER_REGISTER_INFO_NOT_EXIST = 3000506;

    /**
     * @Message("获取 QQ access_token 异常！")
     */
    const QQ_ACCESS_TOKEN_FAIL = 3000507;

    /**
     * @Message("获取 QQ open id 信息失败！")
     */
    const QQ_GET_OPEN_ID_FAIL = 3000508;

    /**
     * @Message("获取 QQ 用户信息失败！")
     */
    const QQ_GET_USER_INFO_FAIL = 3000509;

    /******************** 用户模块错误码 end *****************************************************************************/

    /******************** 文章模块错误码 begin 3000601 ~ 3000700 *********************************************************/

    /**
     * @Message("文章不存在！")
     */
    const ARTICLE_NOT_EXIST_ERROR = 3000601;

    /******************** 文章模块错误码 end *****************************************************************************/

    /******************** 评论模块错误码 begin 3000701 ~ 3000800 *********************************************************/

    /**
     * @Message("今日评论数已达上限！")
     */
    const COMMENT_TOO_MANY_ERROR = 3000701;

    /**
     * @Message("评论不存在！")
     */
    const COMMENT_NOT_EXIST_ERROR = 3000702;

    /******************** 评论模块错误码 end *****************************************************************************/
}