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
    /******************** 公共错误码 begin 10001 ~ 10200 ****************************************************************/
    /**
     * @Message("请求参数错误")
     */
    const REQUEST_PARAMS_INVALID = 10001;

    /**
     * @Message("请先登录！")
     */
    const ACCESS_TOKEN_EMPTY_ERROR = 10002;
    /******************** 公共错误码 end ********************************************************************************/
}