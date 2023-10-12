<?php

namespace console;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command{

    protected static $defaultName = 'test';
    protected static $defaultDescription = 'This is a test';


    public function execute(InputInterface $input, OutputInterface $output): int {
        return Command::SUCCESS;
    }
}