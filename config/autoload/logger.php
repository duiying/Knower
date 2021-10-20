<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

use Monolog\Handler;
use Monolog\Formatter;
use Monolog\Logger;

return [
    'default' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/api.log',
                'level' => Monolog\Logger::INFO,
            ],
        ],
        'formatter' => [
            'class' => Formatter\JsonFormatter::class,
            'constructor' => [
                'batchMode' => Formatter\JsonFormatter::BATCH_MODE_JSON,
                'appendNewline' => true,
            ],
        ],
    ],
    'info' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/api-info.log',
                'level' => Monolog\Logger::INFO,
            ],
        ],
        'formatter' => [
            'class' => Formatter\JsonFormatter::class,
            'constructor' => [
                'batchMode' => Formatter\JsonFormatter::BATCH_MODE_JSON,
                'appendNewline' => true,
            ],
        ],
    ],
    'error' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/api-error.log',
                'level' => Monolog\Logger::ERROR,
            ],
        ],
        'formatter' => [
            'class' => Formatter\JsonFormatter::class,
            'constructor' => [
                'batchMode' => Formatter\JsonFormatter::BATCH_MODE_JSON,
                'appendNewline' => true,
            ],
        ],
    ],
    'debug' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/api-debug.log',
                'level' => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class' => Formatter\JsonFormatter::class,
            'constructor' => [
                'batchMode' => Formatter\JsonFormatter::BATCH_MODE_JSON,
                'appendNewline' => true,
            ],
        ],
    ],
    'warning' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/api-warning.log',
                'level' => Monolog\Logger::WARNING,
            ],
        ],
        'formatter' => [
            'class' => Formatter\JsonFormatter::class,
            'constructor' => [
                'batchMode' => Formatter\JsonFormatter::BATCH_MODE_JSON,
                'appendNewline' => true,
            ],
        ],
    ],
    'notice' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/api-notice.log',
                'level' => Monolog\Logger::NOTICE,
            ],
        ],
        'formatter' => [
            'class' => Formatter\JsonFormatter::class,
            'constructor' => [
                'batchMode' => Formatter\JsonFormatter::BATCH_MODE_JSON,
                'appendNewline' => true,
            ],
        ],
    ],
];
