<?php
declare(strict_types=1);

namespace Dux\Scheduler;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SchedulerCommand extends Command
{

    protected static $defaultName = 'scheduler';
    protected static $defaultDescription = 'Scheduler start service';

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = App::scheduler()->data ?: [['Not Scheduler Jobs']];
        $table = new Table($output);
        $table->setHeaders(['DuxCMS Scheduler Service', date('Y-m-d H:i:s')])
            ->setRows($data);
        $table->render();
        App::scheduler()->run();
        return Command::SUCCESS;
    }
}