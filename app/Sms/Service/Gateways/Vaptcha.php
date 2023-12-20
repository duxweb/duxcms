<?php

namespace App\Sms\Service\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Gateways\Gateway;
use Overtrue\EasySms\Support\Config;
use Overtrue\EasySms\Traits\HasHttpRequest;

class Vaptcha extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL_TEMPLATE = 'http://sms.vaptcha.com/send';

    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $data = $message->getData($this);
        $token = $data['token'];
        unset($data['token']);
        $IDDCode = !empty($to->getIDDCode()) ? $to->getIDDCode() : 86;
        $body = [
            'smsid' => $config->get('smsid'),
            'smskey' => $config->get('smskey'),
            'templateid' => $message->getTemplate($this),
            'countrycode' => $IDDCode,
            'token' => $token,
            'data' => $data,
            'phone' => $to->getNumber(),
        ];
        $result = $this->postJson(self::ENDPOINT_URL_TEMPLATE, $body);
        if ($result->getStatusCode() != 200) {
            throw new GatewayErrorException($result->getBody(), $result->getStatusCode(), $result);
        }
        return $result;
    }
}