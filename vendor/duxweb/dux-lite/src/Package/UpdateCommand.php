<?php
declare(strict_types=1);

namespace Dux\Package;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateCommand extends Command
{

    protected static $defaultName = 'update';
    protected static $defaultDescription = 'Update the application';

    protected function configure(): void
    {
        $this->addArgument(
            'name',
            InputArgument::OPTIONAL,
            'please enter the app name'
        )
            ->addOption('build', null, InputOption::VALUE_REQUIRED, 'whether to compile ui');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $app = $input->getArgument('name');
        $build = $input->getOption('build');

        $helper = $this->getHelper('question');
        $auth = Package::auth($helper, $input, $output);
        if (is_int($auth)) {
            return $auth;
        }

        try {
            Update::main($output, $auth, $app);
        } finally {
            FileSystem::delete(data_path('package'));
        }

        $application = $this->getApplication();
        Package::installOther($application, $output, !!$build);

        return Command::SUCCESS;
    }

}