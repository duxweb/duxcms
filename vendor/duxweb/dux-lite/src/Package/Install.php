<?php

namespace Dux\Package;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Install
{
    public static function main(OutputInterface $output, string $token, string $app): void
    {
        $info = Package::app($token, $app);
        $packages = $info['packages'];
        Add::main($output, $token, $packages);
        Package::updateAppVersion($info);
    }

}