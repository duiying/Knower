<?php

namespace App\Util;

use App\Constant\AppErrorCode;
use Hyperf\Server\Exception\ServerException;
use Throwable;

/**
 * 应用异常类（业务抛出的异常）
 * 需要在 app/Constant/ 目录下新建 AppErrorCode 类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Util
 */
class AppException extends ServerException
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = AppErrorCode::getMessage($code);
        }

        parent::__construct($message, $code, $previous);
    }
}