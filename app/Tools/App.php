<?php

declare(strict_types=1);

namespace App\Tools;

use App\Tools\Listener\SourceListener;
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
        \App\Tools\Config\Permission::Admin($app->getPermission()->get("admin"), $app);

        $app->getEvent()->addListener('tools.magic.source', [new SourceListener, 'data']);
    }


}
