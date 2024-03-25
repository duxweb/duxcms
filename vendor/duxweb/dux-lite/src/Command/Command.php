<?php
declare(strict_types=1);

namespace Dux\Command;

use Symfony\Component\Console\Application;
class Command {

    public static function init(array $commands = []): Application {
        $application = new Application();
        foreach ($commands as $command) {
            $application->add(new $command);
        }
        return $application;
    }



}