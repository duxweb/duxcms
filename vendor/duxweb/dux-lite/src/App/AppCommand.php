<?php
declare(strict_types=1);

namespace Dux\App;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppCommand extends Command {

    protected static $defaultName = 'app:list';
    protected static $defaultDescription = 'List of all applications in the system';



    public function execute(InputInterface $input, OutputInterface $output): int {

        $data = [];
        foreach (App::$registerApp as $vo) {
            $class = new $vo;
            $data[] = [$class->name, $class->description, $class::class];
        }

        $table = new Table($output);
        $table->setHeaders([
                ['Namespace']
            ])
            ->setRows($data);
        $table->render();
        return Command::SUCCESS;
    }

}