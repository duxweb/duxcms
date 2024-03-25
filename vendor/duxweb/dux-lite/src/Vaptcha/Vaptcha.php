<?php

namespace Dux\Vaptcha;

use Dux\App;
use Dux\Handlers\Exception;
use Dux\Handlers\ExceptionBusiness;
use GuzzleHttp\Client;

class Vaptcha
{
    static function Verify(?string $server, ?string $token): void
    {
        $id = App::config('vaptcha')->get('id');
        $key = App::config('vaptcha')->get('key');
        if (!$id || !$key) {
            return;
        }
        if (!$server || !$token) {
            throw new ExceptionBusiness('请先通过人机验证');
        }
        $client = new Client();
        $result = $client->post($server, [
            'json' => [
                'id' => $id,
                'secretkey' => $key,
                'scene' => 0,
                'token' => $token,
                'ip' => get_ip()
            ]
        ]);
        if ($result->getStatusCode() != 200) {
            throw new Exception('验证服务器失败，请重试');
        }
        $data = json_decode($result->getBody(), true);
        if (!$data['success']) {
            throw new Exception($data['msg'] ?: $result->getBody());
        }
    }

}