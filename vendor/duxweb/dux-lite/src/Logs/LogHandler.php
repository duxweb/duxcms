<?php
declare(strict_types=1);

namespace Dux\Logs;

use Dux\App;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class LogHandler {

    public static function init(string $name, Level $level): Logger {
        $log = new Logger($name);
        $log->pushHandler(new StreamHandler(App::$dataPath . '/logs/' . $name . '.log', $level));
        return $log;
    }
}