<?php

namespace App\System\Enum;

use Dux\App;

enum PlatformEnum: string
{
    // web
    case WEB = 'web';
    // wap
    case WAP = 'wap';
    // APP
    case APP = 'app';
    // 微信公众号
    case WECHAT = 'wechat';
    // 微信小程序
    case WEAPP = 'weapp';

    public function name(): string
    {
        return match ($this) {
            self::WEB => '电脑端',
            self::WAP => 'H5手机端',
            self::APP => 'APP',
            self::WECHAT => '微信公众号',
            self::WEAPP => '微信小程序',
        };
    }


    public static function list(): array
    {
        return array_map(
            fn(PlatformEnum $item) => $item->value,
            PlatformEnum::cases()
        );
    }

    /**
     * 请求地址
     * @return string|null
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function url(): ?string
    {
        return match ($this) {
            self::WEB, self::WAP, self::APP, self::WECHAT => App::config('use')->get('domain.web'),
            default => null
        };
    }


}