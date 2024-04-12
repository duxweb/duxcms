<?php
declare(strict_types=1);

namespace Dux\Package;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class YarnCommand extends Command
{

    protected static $defaultName = 'package:yarn';
    protected static $defaultDescription = 'yarn console';

    protected function configure(): void
    {
        $this
            ->setDescription('Execute yarn commands via PHP.')
            ->addArgument('cmd', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The yarn command to run.')
            ->setHelp('This command allows you to run yarn commands...');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $yarnCommand = $input->getArgument('cmd');

        $executableFinder = new \Symfony\Component\Process\ExecutableFinder();
        $yarnPath = $executableFinder->find('yarn');

        if (!$yarnPath) {
            throw new \Exception('Path to yarn not found');
        }


//        $process = new Process(['yarn', 'config', 'set', 'registry', 'https://registry.npmmirror.com']);
//        $process->run();

        $command = array_merge([$yarnPath], is_array($yarnCommand) ? $yarnCommand : [$yarnCommand]);
        $workingDirectory = base_path('web');
        $process = new Process($command, $workingDirectory);
        $process->setTimeout(3600);

        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return Command::SUCCESS;
    }

}