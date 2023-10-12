<?php

declare(strict_types=1);

namespace App\Tools;

use App\Tools\Config\Menu;
use App\Tools\Config\Route;
use App\Tools\Scheduler\Test;
use Dux\App\AppExtend;
use Dux\Bootstrap;

/**
 * Application Registration
 */
class App extends AppExtend
{
    public function register(Bootstrap $app): void
    {
        \App\Tools\Config\Menu::Admin($app->getMenu()->get("admin"), $app);

        $app->scheduler->add('* * * * *', function () {
            \Dux\App::log('debug')->info('xxx');
        });

        $app->scheduler->add('* * * * *', [Test::class, 'hello']);

    }

}
