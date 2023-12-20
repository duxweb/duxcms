<?php

namespace App\Sms\Service\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Gateways\Gateway;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;

class Unisms extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL_TEMPLATE = 'https://uni.apistd.com?';

    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $data = $message->getData($this);
        $body = [
            'to' => $to->getNumber(),
            'signature' => $config->get('sign'),
            'templateId' => $message->getTemplate($this),
            'templateData' => $data,
        ];

        $nonce = bin2hex(random_bytes(10));
        $timestamp = round(microtime(true) * 1000);
        $sign = 'accessKeyId='.$config->get('accessKeyId').'&action=sms.message.send&algorithm=hmac-sha256&nonce='.$nonce.'&timestamp=' . $timestamp;
        $signature = base64_encode(hash_hmac('sha256', $sign, $config->get('accessKeySecret'), true));
        $sign .= '&signature=' . $signature;
        $result = $this->postJson(self::ENDPOINT_URL_TEMPLATE . $sign, $body);
        if ($result->getStatusCode() != 200) {
            throw new GatewayErrorException($result->getBody(), $result->getStatusCode(), $result);
        }
        $result = json_decode($result->getBody()?->getContents() ?: '', true);
        if (!$result) {
            throw new GatewayErrorException('Request has no return', 500, $result);
        }
        if ($result['code'] != 0) {
            throw new GatewayErrorException($result['message'], $result['code'], $result);
        }
        return $result['data'];
    }
}