<?php

namespace App\Sms\Service;

use App\Sms\Models\SmsEmail;
use App\System\Service\Config;
use DI\DependencyException;
use DI\NotFoundException;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;

class Email
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
     * @param string $mail
     * @param int $channel
     * @return int
     * @throws DependencyException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public static function code(int|string $label, string $mail, int $channel = 0): int
    {
        $code = Utils::getCode($channel, $mail);
        self::send($label, $mail, [
            'content' => $code,
            'expire' => Config::getValue('sms_expire', 30),
        ]);
        return (int)$code;
    }

    /**
     * 验证验证码
     * @param string $mail
     * @param string $code
     * @param int $channel
     * @param bool $delete
     * @return void
     */
    public static function verify(string $mail, string $code, int $channel = 0, bool $delete = true): void
    {
        Utils::verifyCode($mail, $code, $channel, $delete);
    }

    /**
     * 发送短信
     * @param int|string $label
     * @param string $email
     * @param array $params
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function send(int|string $label, string $email, array $params = []): void
    {
        if (is_int($label)) {
            $info = SmsEmail::query()->find($label);
        } else {
            $info = SmsEmail::query()->where('label', $label)->first();
        }
        if (!$info) {
            throw new ExceptionBusiness('短信模板不存在');
        }

        $emailConfig = \App\System\Service\Config::getValue("email_*", []);

        $mailer = new \Nette\Mail\SmtpMailer([
            'host' => $emailConfig['email_host'],
            'port' => $emailConfig['email_port'],
            'username' => $emailConfig['email_username'],
            'password' => $emailConfig['email_password'],
            'secure' => $emailConfig['email_secure'],
            'timeout' => $emailConfig['email_timeout'] ?: 10,
        ]);

        $tags = [
            'site' => App::config('use')->get('app.name'),
            'title' => $info->name,
            ...$params
        ];

        $content = $info->content;
        foreach ($tags as $key => $value) {
            $content = str_replace($content, $key, $value);
        }

        $mail = new \Nette\Mail\Message;
        $mail->setFrom($emailConfig['email_name'] . ' <'. $emailConfig['username'] .'>')
            ->addTo($email)
            ->setSubject($info->name)
            ->setHtmlBody($content);

        $mailer->send($mail);
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