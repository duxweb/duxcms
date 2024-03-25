<?php
declare(strict_types=1);

namespace Dux\Database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command {

    protected static $defaultName = 'db:list';
    protected static $defaultDescription = 'View the list of automatic migration models';


    public function execute(InputInterface $input, OutputInterface $output): int {

        $table = new Table($output);
        $table->setHeaders([
            [new TableCell("Auto Migrate Models", ['colspan' => 1])],
        ]);
        $list = \Dux\App::dbMigrate()->migrate;
        $data = [];
        foreach ($list as $class) {
            $data[] = [$class];
        }
        $table->setRows($data);
        $table->render();

        return Command::SUCCESS;
    }
}