<?php
declare(strict_types=1);

namespace Dux\Database;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupCommand extends Command {

    protected static $defaultName = 'db:backup';
    protected static $defaultDescription = 'Backup the database';


    public function execute(InputInterface $input, OutputInterface $output): int {

        $dirPath = data_path('backup/');
        if (!is_dir($dirPath)) {
            mkdir($dirPath);
        }

        $filePath = $dirPath.date('Y-m-d-His').'.sql';

        $config = App::config("database")->get("db.drivers.default");

        if ($config['driver'] != 'mysql') {
            throw new \Exception('Support mysql database only');
        }

        $command = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'where' : 'which';
        $pathFinder = new Process([$command, 'mysqldump']);
        $pathFinder->run();

        if (!$pathFinder->isSuccessful()) {
            throw new ProcessFailedException($pathFinder);
        }
        $path = trim($pathFinder->getOutput());

        $databaseCommand = 'mysqldump --add-drop-table'
            . ' --skip-add-locks --skip-comments --skip-set-charset --tz-utc --set-gtid-purged=OFF'
            . ' --host=' . escapeshellarg($config['host'])
            . ' --port=' . escapeshellarg($config['port'])
            . ' --user=' . escapeshellarg($config['username'])
            . ' --password=' . escapeshellarg($config['password'])
            . ' ' . escapeshellarg($config['database'])
            . ' --result-file=' . escapeshellarg($filePath)
            . ' --routines';

        $process = Process::fromShellCommandline($databaseCommand);
        $process->mustRun();

        $output->writeln("<info>Backup database successfully</info>");

        return Command::SUCCESS;
    }
}