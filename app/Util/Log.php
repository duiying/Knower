<?php

namespace App\Util;

use Hyperf\Utils\ApplicationContext;

class Log
{
    public static function info($message, array $context = [], $name = 'app')
    {
        self::getLogger($name, 'info')->info($message, $context);
    }

    public static function error($message, array $context = [], $name = 'app')
    {
        self::getLogger($name, 'error')->error($message, $context);
    }

    public static function debug($message, array $context = [], $name = 'app')
    {
        self::getLogger($name, 'debug')->debug($message, $context);
    }

    public static function warning($message, array $context = [], $name = 'app')
    {
        self::getLogger($name, 'warning')->warning($message, $context);
    }

    public static function notice($message, array $context = [], $name = 'app')
    {
        self::getLogger($name, 'notice')->notice($message, $context);
    }

    public static function getLogger($name = 'app', $group = 'info')
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name, $group);
    }
}