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
}