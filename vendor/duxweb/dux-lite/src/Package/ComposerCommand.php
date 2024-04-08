<?php
declare(strict_types=1);

namespace Dux\Package;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ComposerCommand extends Command
{

    protected static $defaultName = 'package:composer';
    protected static $defaultDescription = 'composer console';

    protected function configure(): void
    {
        $this
            ->setDescription('Execute execute commands via PHP.')
            ->addArgument('cmd', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The execute command to run.')
            ->setHelp('This command allows you to run yarn commands...');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerCommand = $input->getArgument('cmd');

        $executableFinder = new \Symfony\Component\Process\ExecutableFinder();
        $composerPath = $executableFinder->find('composer');

        if (!$composerPath) {
            throw new \Exception('Path to composer not found');
        }


        $composerPathFinder = Process::fromShellCommandline('/usr/bin/which composer');
        $composerPathFinder->run();

        if (!$composerPathFinder->isSuccessful()) {
            throw new ProcessFailedException($composerPathFinder);
        }
        $composerPath = trim($composerPathFinder->getOutput());


        $command = array_merge([$composerPath], is_array($composerCommand) ? $composerCommand : [$composerCommand]);
        $workingDirectory = base_path();
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