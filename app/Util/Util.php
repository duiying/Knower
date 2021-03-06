<?php

namespace App\Util;

use App\Constant\AppErrorCode;
use App\Util\Exception\AppException;
use App\Util\Redis;

/**
 * 常用工具类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\Util
 */
class Util
{
    /**
     * 对象转数组
     *
     * @param $obj
     * @return array
     */
    public static function object2Array($obj)
    {
        return json_decode(json_encode($obj), true);
    }

    /**
     * 格式化查找接口结果
     *
     * @param $p
     * @param $size
     * @param $total
     * @param $list
     * @return array
     */
    public static function formatSearchRes($p, $size, $total = 0, $list = [])
    {
        return [
            'p'         => $p,
            'size'      => $size,
            'total'     => $total,
            'next'      => ($p * $size < $total) && ($p * $size > 0) ? 1 : 0,
            'list'      => $list
        ];
    }

    /**
     * 一个字符串中是否包含另外一个字符串
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function contain($haystack, $needle)
    {
        return strstr($haystack, $needle) !== false;
    }

    /**
     * 协程休眠
     *
     * @param int $seconds
     */
    public static function sleep($seconds = 1)
    {
        \Swoole\Coroutine::sleep($seconds);
    }

    /**
     * 获取当前格式化时间
     *
     * @return string
     */
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 获取当天开始时间
     *
     * @return string
     */
    public static function todayBeginTime()
    {
        return date('Y-m-d 00:00:00');
    }

    /**
     * 获取当天结束时间
     *
     * @return string
     */
    public static function todayEndTime()
    {
        return date('Y-m-d 23:59:59');
    }

    /**
     * 获取本周开始时间
     *
     * @return string
     */
    public static function getWeekBeginTime()
    {
        $date = date('Y-m-d');
        $w = date('w', strtotime($date));
        return date('Y-m-d 00:00:00', strtotime("$date -" . ($w ? $w - 1 : 6) . ' days'));
    }

    /**
     * 获取本周结束时间
     *
     * @return string
     */
    public static function getWeekEndTime()
    {
        $date = date('Y-m-d');
        $w = date('w', strtotime($date));
        $weekStart = date('Y-m-d', strtotime("$date -" . ($w ? $w - 1 : 6) . ' days'));
        return date('Y-m-d 23:59:59', strtotime("$weekStart +6 days"));
    }

    /**
     * 获取当月开始时间
     *
     * @return string
     */
    public static function getMonthBeginTime()
    {
        return date('Y-m-d H:i:s', strtotime("first day of this month 00:00:00"));
    }

    /**
     * 获取当月结束时间
     *
     * @return string
     */
    public static function getMonthEndTime()
    {
        return date('Y-m-d H:i:s', strtotime("last day of this month 23:59:59"));
    }

    /**
     * 获取当年开始时间
     *
     * @return string
     */
    public static function getYearBeginTime()
    {
        return date('Y-01-01 00:00:00');
    }

    /**
     * 获取当年结束时间
     *
     * @return string
     */
    public static function getYearEndTime()
    {
        $year = date('Y');
        $nextYear = $year + 1;
        return date('Y-m-d H:i:s', strtotime("$nextYear-01-01 00:00:00") - 1);
    }

    /**
     * 获取 traceId
     *
     * @return string
     */
    public static function getTraceId()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * 生成 Token
     *
     * @return string
     */
    public static function generateToken()
    {
        return md5(uniqid() . time() . mt_rand(10000, 99999));
    }

    /**
     * 获取 redis 分布式锁
     *
     * @param $key
     * @param int $timeout
     * @param string $poolName
     * @return bool
     */
    public static function getLock($key, $timeout = 1, $poolName = 'default')
    {
        return Redis::instance($poolName)->set($key, 1, ['nx', 'ex' => $timeout]);
    }

    /**
     * 删除 redis 分布式锁
     *
     * @param $key
     * @param string $poolName
     * @return int
     */
    public static function delLock($key, $poolName = 'default')
    {
        return Redis::instance($poolName)->del($key);
    }

    /**
     * 入队（基于 redis）
     *
     * @param $key
     * @param array $value
     * @param string $poolName
     * @return bool|int
     */
    public static function enqueueByRedis($key, $value = [], $poolName = 'default')
    {
        return Redis::instance($poolName)->lPush($key, json_encode($value));
    }

    /**
     * 出队（基于 redis）
     *
     * @param $key
     * @param string $poolName
     * @return mixed
     */
    public static function dequeueByRedis($key, $poolName = 'default')
    {
        $data = Redis::instance($poolName)->rPop($key);
        return json_decode($data, true);
    }

    /**
     * 将数组转换为对应的 redis key
     *
     * @param array $data
     * @return string
     */
    public static function generateRedisKeyByArrayData($data = [])
    {
        return md5(serialize($data));
    }

    /**
     * 清洗请求数据
     *
     * @param $requestData
     * @param $rules
     * @return array
     */
    public static function sanitize($requestData, $rules)
    {
        if (empty($requestData) || empty($rules)) return [];

        $sanitizedData = [];

        foreach ($requestData as $k => $v) {
            if (isset($rules[$k])) {
                // 对字符串类型的字段进行 trim 操作
                if (self::contain($rules[$k], 'required') && self::contain($rules[$k], 'string')) {
                    $v = trim($v);
                    if (empty($v)) throw new AppException(AppErrorCode::FIELD_EMPTY_EXCEPTION);
                }

                // 字段类型转换
                if (self::contain($rules[$k], 'string')) {
                    $sanitizedData[$k] = (string)$v;
                } else if (self::contain($rules[$k], 'integer')) {
                    $sanitizedData[$k] = (int)$v;
                } else {
                    $sanitizedData[$k] = (string)$v;
                }
            }
        }

        return $sanitizedData;
    }

    /**
     * ids 转为 ID 数组
     *
     * @param $ids
     * @return array|false|string[]
     */
    public static function ids2IdArr($ids)
    {
        $ids = rtrim($ids, ',');
        if (empty($ids)) return [];
        return array_unique(explode(',', $ids));
    }

    /**
     * 对象数组转数组
     *
     * @param array $objList
     * @return array
     */
    public static function objArr2Arr($objList = [])
    {
        if (empty($objList)) return [];

        array_walk($objList, function (&$obj) {
            $obj = (array)$obj;
        });

        return $objList;
    }

    /**
     * 二维数组根据某个字段去重
     *
     * @param $arr
     * @param $column
     * @return array
     */
    public static function twoDimensionalArrayUnique($arr, $column)
    {
        if (empty($arr)) return [];

        $tmpArr = [];

        foreach ($arr as $k => $v) {
            if (!isset($tmpArr[$v[$column]])) {
                $tmpArr[$v[$column]] = $v;
            }
        }

        return array_values($tmpArr);
    }

    /**
     * 人性化时间
     *
     * @param $time
     * @return string
     */
    public static function formatTime($time)
    {
        $intTime = is_string($time) ? strtotime($time) : $time;
        $now = time();
        $diff = $now - $intTime;
        if ($diff < 60) {
            $str = '刚刚';
        } elseif ($diff < 60 * 60) {
            $min = floor($diff / 60);
            $str = $min . ' 分钟前';
        } elseif ($diff < 60 * 60 * 24) {
            $h = floor($diff / (60 * 60));
            $str = $h . ' 小时前';
        } else {
            $str = date('Y-m-d H:i', $intTime);
        }
        return $str;
    }

    /**
     * 企业微信通知
     *
     * @param string $msg
     * @return bool
     */
    public static function QYWechatNotify($msg = '')
    {
        if (empty($msg)) {
            return false;
        }
        if (intval(env('QY_WECHAT_SWITCH')) === 0) {
            return false;
        }

        $corpId = env('QY_WECHAT_CORP_ID');
        $secret = env('QY_WECHAT_SECRET');
        $agentId = intval(env('QY_WECHAT_AGENT_ID'));

        if (empty($corpId) || empty($secret) || empty($agentId)) {
            return false;
        }

        // 1、先获取 access_token
        $getTokenUrl = sprintf('https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s', $corpId, $secret);
        $getTokenRes = file_get_contents($getTokenUrl);
        if (empty($getTokenRes)) {
            Log::error('获取企业微信 access_token 返回为空');
            return false;
        }
        $getTokenArr = json_decode($getTokenRes, true);
        if (empty($getTokenArr) || !isset($getTokenArr['access_token']) || empty($getTokenArr['access_token'])) {
            Log::error('获取企业微信 access_token 返回异常', ['getTokenRes' => $getTokenRes]);
            return false;
        }
        $accessToken = $getTokenArr['access_token'];

        // 2、发送文本信息
        $sendTextMsgParams = [
            'touser'    => '@all',
            // 文本格式
            'msgtype'   => 'text',
            'agentid'   => $agentId,
            'text' => [
                'content' => $msg
            ]
        ];

        $sendMsgUrl = sprintf('https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=%s', $accessToken);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $sendMsgUrl);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($sendTextMsgParams));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', "Accept:application/json"]);
        $sendMsgRes = curl_exec($curl);
        curl_close($curl);
        if (!is_string($sendMsgRes)) {
            Log::error('发送企业微信通知失败', ['sendMsgRes' => $sendMsgRes, 'error' => curl_error($curl)]);
            return false;
        }
        $sendMsgArr = json_decode($sendMsgRes, true);
        if (empty($sendMsgArr)) {
            Log::error('发送企业微信通知返回为空');
            return false;
        }
        Log::info('发送企业微信通知成功', ['msg' => $msg, 'sendMsgArr' => $sendMsgArr]);
        return true;
    }
}