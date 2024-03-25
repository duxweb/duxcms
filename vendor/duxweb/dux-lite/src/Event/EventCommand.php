<?php
declare(strict_types=1);

namespace Dux\Event;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventCommand extends Command {

    protected static $defaultName = 'event';
    protected static $defaultDescription = 'show event list';

    protected function configure(): void {
        $this->addArgument(
            'name',
            InputArgument::OPTIONAL,
            'please enter the event name'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $name = $input->getArgument("name");
        if ($name) {
            $list = [$name => App::event()->registers[$name]];
        }else {
            $list = App::event()->registers;
        }
        $data = [];
        foreach ($list as $name => $items) {
            $table = new Table($output);
            $data = [];
            foreach ($items as $item) {
                $data[] = [$item];
            }
            $table
                ->setHeaders([
                    [new TableCell((string)$name, ['colspan' => 1])],
                ])
                ->setRows($data);
            $table->render();

        }
        return Command::SUCCESS;
    }
}