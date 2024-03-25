<?php
declare(strict_types=1);

namespace Dux\Database;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RestoreCommand extends Command {

    protected static $defaultName = 'db:restore';
    protected static $defaultDescription = 'restore the database';


    public function execute(InputInterface $input, OutputInterface $output): int {

        $dirPath = data_path('backup/');
        if (!is_dir($dirPath)) {
            mkdir($dirPath);
        }
        $files = glob($dirPath .'*.sql');
        sort($files);
        $latestFile = end($files);

        $config = App::config("database")->get("db.drivers.default");

        if ($config['driver'] != 'mysql') {
            throw new \Exception('Support mysql database only');
        }

        $command = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'where' : 'which';
        $pathFinder = new Process([$command, 'mysql']);
        $pathFinder->run();

        if (!$pathFinder->isSuccessful()) {
            throw new ProcessFailedException($pathFinder);
        }
        $path = trim($pathFinder->getOutput());

        $command = 'cat ' . escapeshellarg($latestFile)
            . " | mysql --no-beep"
            . ' --host=' . escapeshellarg($config['host'])
            . ' --port=' . escapeshellarg($config['port'])
            . ' --user=' . escapeshellarg($config['username'])
            . ' --password=' . escapeshellarg($config['password'])
            . ' --database=' . escapeshellarg($config['database']);


        $process = Process::fromShellCommandline($command);
        $process->mustRun();

        $output->writeln("<info>Backup database successfully</info>");

        return Command::SUCCESS;
    }
}