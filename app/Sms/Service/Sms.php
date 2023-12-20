<?php

namespace App\Sms\Service;

use App\Sms\Models\SmsTpl;
use App\Sms\Service\Gateways\Unisms;
use App\Sms\Service\Gateways\Vaptcha;
use App\System\Service\Config;
use DI\DependencyException;
use DI\NotFoundException;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use RedisException;

class Sms
{
    public static array $config = [];

    /**
     * 全局配置
     * @param array $config
     * @return void
     */
    public static function config(array $config = []): void
    {
        self::$config = $config;
    }

    /**
     * 获取验证码
     * @param int|string $label
     * @param string $tel
     * @param int $channel
     * @param array $extend
     * @return int
     * @throws RedisException
     */
    public static function code(int|string $label, string $tel, int $channel = 0, array $extend = []): int
    {
        $redis = App::redis();
        $redisKey = "sms.code.$channel.value.$tel";
        $redisLock = "sms.code.$channel.lock.$tel";
        $redisLimit = "sms.code.$channel.limit.$tel";
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

        self::send($label, $tel, [
            'code' => $code,
            'expire' => Config::getValue('sms_expire', 30),
        ], $extend);
        return (int)$code;
    }

    /**
     * 验证验证码
     * @param string $tel
     * @param string $code
     * @param int $channel
     * @param bool $delete
     * @return void
     * @throws RedisException
     */
    public static function verify(string $tel, string $code, int $channel = 0, bool $delete = true): void
    {
        $redis = App::redis();
        $redisKey = "sms.code.$channel.value.$tel";
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

    /**
     * 发送短信
     * @param int|string $label
     * @param string $tel
     * @param array $params
     * @param array $extend
     * @return void
     * @throws InvalidArgumentException
     */
    public static function send(int|string $label, string $tel, array $params = [], array $extend = []): void
    {
        if (is_int($label)) {
            $info = SmsTpl::query()->find($label);
        } else {
            $info = SmsTpl::query()->where('label', $label)->first();
        }
        if (!$info) {
            throw new ExceptionBusiness('短信模板不存在');
        }
        $easySms = new EasySms(self::$config);

        $easySms->extend('vaptcha', function ($gatewayConfig) {
            return new Vaptcha($gatewayConfig);
        });
        $easySms->extend('unisms', function ($gatewayConfig) {
            return new Unisms($gatewayConfig);
        });

        $sendData = [];
        if (!$info->type) {
            $content = $info->content ?: '';
            foreach ($params as $key => $vo) {
                $content = str_replace('{' . $key . '}', $vo, $content);
            }
            $sendData['content'] = $content;
        } else {
            $data = [];
            foreach ($info->params as $v) {
                foreach ($params as $key => $vo) {
                    $v['value'] = str_replace('{' . $key . '}', $vo, $v['value']);
                }
                $data[$v['name']] = $v['value'];
            }
            $sendData['template'] = $info->tpl;
            $sendData['data'] = array_filter([...$data, ...$extend]);
        }
        try {
            $easySms->send($tel, $sendData, [$info->method]);

        } catch (NoGatewayAvailableException $e) {
            foreach ($e->getExceptions() as $vo) {
                throw new ExceptionBusiness($vo->getMessage());
            }
        }
    }

    /**
     * 发送队列短信
     * @param int $id
     * @param string $tel
     * @param array $params
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function sendQueue(int $id, string $tel, array $params = []): void
    {
        App::queue()->add(self::class, "send", [$id, $tel, $params])->send();
    }

}