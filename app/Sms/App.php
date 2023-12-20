<?php

declare(strict_types=1);

namespace App\Sms;

use App\Sms\Service\Sms;
use Dux\Bootstrap;

/**
 * Application Registration
 */
class App extends \Dux\App\AppExtend
{

    public function register(Bootstrap $app): void {
        // 注册短信配置
        Sms::config(\Dux\App::config('sms')->all());
    }

}
