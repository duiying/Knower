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
}