<?php

namespace Dux\Package;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Uninstall
{
    public static function main(OutputInterface $output,string $token, string $app): void
    {
        $info = Package::app($token, $app);
        $packages = $info['packages'];
        Del::main($output, $packages);

        $configFile = base_path('app.json');
        $appJson = [];
        if (is_file($configFile)) {
            $appJson = Package::getJson($configFile);
        }
        $apps = $appJson['apps'] ?: [];
        unset($apps[$app]);
        $appJson['apps'] = $apps;
        Package::saveJson($configFile, $appJson);
    }

}