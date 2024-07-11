<?php

namespace App\Tools\Service;

use App\System\Enum\PlatformEnum;
use App\Wechat\Service\Wechat;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;

class Qrcode
{
    public static function generate(PlatformEnum $platform, string $url, string $path, array $params = [], array $config = []): string
    {
        $cacheKey = [
            'url' => $url,
            'path' => $path,
            'params' => $params,
            'platform' => $platform->value
        ];
        $cacheKeyStr = 'qrcode.' . md5(json_encode($cacheKey));
        if (App::cache()->has($cacheKeyStr)) {
            return App::cache()->get($cacheKeyStr);
        }

        $path = trim($path, "/");
        $pathLink = str_contains($path, '?') ? '&' : '?';

        switch ($platform) {
            case PlatformEnum::WEAPP:
                $data = [
                    'check_path' => false,
                ];
                $data['path'] = $path . $pathLink . http_build_query($params);
                $buffer = Wechat::mini($config['mini_app_id'])->getClient()->postJson('/wxa/getwxacode', $data)->getContent();

                $bufferData = json_decode($buffer, true);
                if ($bufferData && $bufferData['errcode']) {
                    throw new ExceptionBusiness($bufferData['errcode'] . ': ' . $bufferData['errmsg']);
                }
                break;
            default:
                $tmpFile = tempnam(sys_get_temp_dir(), 'qrcode_');
                $data = $url . $path . $pathLink . http_build_query($params);
                (new \chillerlan\QRCode\QRCode())->render($data, $tmpFile);
                $buffer = file_get_contents($tmpFile);
                unlink($tmpFile);
        }


        $basename = bin2hex(random_bytes(10));
        $filename = sprintf('%s.%0.8s', $basename, 'png');
        $publicUrl = "/qrcode/$filename";
        App::storage()->write($publicUrl, $buffer);
        $url = App::storage()->publicUrl($publicUrl);
        App::cache()->set($cacheKeyStr, $url, 60 * 60 * 24);
        return $url;
    }

}