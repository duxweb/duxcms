<?php
declare(strict_types=1);

namespace Dux\Database;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{

    protected static $defaultName = 'db:sync';
    protected static $defaultDescription = 'Synchronize model data tables and fields';

    protected function configure(): void
    {
        $this->addArgument(
            'app',
            InputArgument::OPTIONAL,
            'please enter the app name'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $app = $input->getArgument('app') ?: '';
        App::dbMigrate()->migrate($output, $app);
        $output->writeln("<info>Sync database successfully</info>");
        return Command::SUCCESS;
    }

}