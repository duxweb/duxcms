<?php
declare(strict_types=1);

namespace Dux\Database;

use Clockwork\DataSource\EloquentDataSource;
use Dux\App;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;

class Db
{
    public static function init(array $configs): Manager
    {
        $capsule = new Manager;
        foreach ($configs as $key => $config) {
            $capsule->addConnection($config, $key);
        }
        $event = new Dispatcher(new Container);
        $capsule->setEventDispatcher($event);
        $capsule->bootEloquent();

        $status = App::config('use')->get('clock');
        if ($status) {
            $source = new EloquentDataSource(
                $capsule->getDatabaseManager(),
                $event,
            );
            clock()->addDataSource($source);
            $source->listenToEvents();
        }

        return $capsule;
    }
}