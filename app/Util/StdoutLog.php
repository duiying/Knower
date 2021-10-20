<?php

namespace App\Util;

class StdoutLog
{
    public static function print($data, $message = '')
    {
        $now = date('Y-m-d H:i:s');
        $message && $message .= ' ';
        $type = gettype($data);

        echo PHP_EOL;
        echo "\033[1;32;5;9m========== [START] DATE:{$now} TYPE:{$type} {$message}==========\033[0m" . PHP_EOL;

        if (is_array($data))    print_r($data);
        if (is_string($data))   echo $data . PHP_EOL;
        if (is_int($data))      echo $data . PHP_EOL;
        if (is_bool($data))     echo $data ? 'TRUE' . PHP_EOL : 'FALSE' . PHP_EOL;
        if (is_null($data))     echo 'NULL' . PHP_EOL;
        if (is_double($data))   echo $data . PHP_EOL;
        if (is_object($data))   print_r($data);

        echo "\033[1;32;5;9m========== [END] ================================================\033[0m" . PHP_EOL . PHP_EOL;
    }
}