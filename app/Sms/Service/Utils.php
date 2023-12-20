<?php

namespace App\Sms\Service;

use App\System\Service\Config;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;

class Utils
{

    public static function getCode(int $channel, string $receive): int
    {

        $redis = App::redis();
        $redisKey = "sms.code.$channel.value.$receive";
        $redisLock = "sms.code.$channel.lock.$receive";
        $redisLimit = "sms.code.$channel.limit.$receive";
        $code = random_int(100000, 999999);
        // 限制获取间隔
        if ($redis->exists($redisLock)) {
            throw new ExceptionBusiness('验证码获取太频繁，请稍后再试');
        }
        $redis->setex($redisLock, Config::getValue('sms_interval', 1) * 60, $code);
        // 限制x分钟x次
        if ($redis->exists($redisLimit)) {
            $limit = $redis->get($redisLimit);
            // 超过最大获取次数
            if ($limit >= Config::getValue('sms_num', 3)) {
                throw new ExceptionBusiness('验证码获取超限，请稍后再试');
            }
            // 增加获取次数
            $redis->setex($redisLimit, $redis->ttl($redisLimit), $limit + 1);
        } else {
            // 设置验证码次数
            $redis->setex($redisLimit, Config::getValue('sms_time', 5), 1);
        }
        // 设置验证码
        $redis->setex($redisKey, Config::getValue('sms_expire', 30) * 60, $code);

        return $code;
    }

    public static function verifyCode(string $receive, string $code, int $channel = 0, bool $delete = true): void
    {
        $redis = App::redis();
        $redisKey = "sms.code.$channel.value.$receive";
        if (!$redis->exists($redisKey)) {
            throw new ExceptionBusiness('验证码不正确或已过期');
        }
        $val = $redis->get($redisKey);
        if ($code != $val) {
            throw new ExceptionBusiness('验证码不正确或已过期');
        }
        // 验证后失效
        if ($delete) {
            $redis->del($redisKey);
        }
    }
}