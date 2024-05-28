<?php
declare(strict_types=1);

namespace Dux\Logs;

use Dux\App;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;

class LogHandler {

    public static function init(string $name, Level $level): Logger {
        $log = new Logger($name);
        $log->pushHandler(new RotatingFileHandler(App::$dataPath . '/logs/' . $name . '.log', 15, $level, true, 0777));
        return $log;
    }
}