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
     * @throws InvalidArgumentException
     */
    public static function code(int|string $label, string $tel, int $channel = 0, array $extend = []): int
    {
        $code = Utils::getCode($channel, $tel);
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
     */
    public static function verify(string $tel, string $code, int $channel = 0, bool $delete = true): void
    {
        Utils::verifyCode($tel, $code, $channel, $delete);
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
        $easySms = new EasySms(self::$config);

        $easySms->extend('vaptcha', function ($gatewayConfig) {
            return new Vaptcha($gatewayConfig);
        });
        $easySms->extend('unisms', function ($gatewayConfig) {
            return new Unisms($gatewayConfig);
        });

        if (is_int($label)) {
            $info = SmsTpl::query()->find($label);
        } else {
            $info = SmsTpl::query()->where('label', $label)->first();
        }
        if (!$info) {
            throw new ExceptionBusiness('短信模板不存在');
        }

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