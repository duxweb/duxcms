<?php
declare(strict_types=1);

namespace Dux\Queue;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QueueCommand extends Command
{

    protected static $defaultName = 'queue';
    protected static $defaultDescription = 'Queue start service';

    protected function configure(): void
    {
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'please enter the queue name'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $version = \Composer\InstalledVersions::getVersion('duxweb/dux-lite');
        $table = new Table($output);
        $table->setHeaders(array('DuxCMS Queue Service'))
            ->setRows(array(
                array('Dux Lite: ' . $version),
                array('Run Time: ' . date('Y-m-d H:i:s')),
            ));
        $table->render();
        App::queue()->process($name);
        return Command::SUCCESS;
    }
}