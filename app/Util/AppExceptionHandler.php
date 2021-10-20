<?php

namespace App\Util;

use App\Constant\AppErrorCode;
use App\Constant\CommonConstant;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 全局异常处理器
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Exception\Handler
 */
class AppExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // 验证器异常
        if ($throwable instanceof ValidationException) {
            // 格式化输出
            $data = json_encode([
                CommonConstant::API_CODE      => AppErrorCode::PARAMS_INVALID,
                CommonConstant::API_MESSAGE   => $throwable->validator->errors()->first(),
                CommonConstant::API_DATA      => null
            ], JSON_UNESCAPED_UNICODE);

            // 阻止异常冒泡
            $this->stopPropagation();

            return $response->withStatus(200)->withBody(new SwooleStream($data));
        }

        // 应用异常
        if ($throwable instanceof AppException) {
            // 格式化输出
            $data = json_encode([
                CommonConstant::API_CODE      => $throwable->getCode(),
                CommonConstant::API_MESSAGE   => $throwable->getMessage(),
                CommonConstant::API_DATA      => null
            ], JSON_UNESCAPED_UNICODE);

            // 阻止异常冒泡
            $this->stopPropagation();

            Log::error('业务抛出异常！', ['code' => $throwable->getCode(), 'msg' => $throwable->getMessage(), 'trace' => $throwable->getTrace()]);

            return $response->withStatus(200)->withBody(new SwooleStream($data));
        }


        // 其它异常
        // 格式化输出
        $data = json_encode([
            CommonConstant::API_CODE      => $throwable->getCode() == 0 ? AppErrorCode::TRIGGER_EXCEPTION : $throwable->getCode(),
            CommonConstant::API_MESSAGE   => '服务产生错误！原因：'. $throwable->getMessage(),
            CommonConstant::API_DATA      => null
        ], JSON_UNESCAPED_UNICODE);

        // 阻止异常冒泡
        $this->stopPropagation();

        Log::error('服务产生错误！', ['code' => $throwable->getCode(), 'msg' => $throwable->getMessage(), 'trace' => $throwable->getTrace()]);

        return $response->withStatus(200)->withBody(new SwooleStream($data));
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理
     *
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}