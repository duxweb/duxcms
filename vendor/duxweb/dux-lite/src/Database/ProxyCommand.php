<?php
declare(strict_types=1);

namespace Dux\Database;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ProxyCommand extends Command
{

    protected static $defaultName = 'db:proxy';
    protected static $defaultDescription = 'start the umyproxy thread pool';


    protected function configure(): void
    {
        $this->addOption(
            'life',
            null,
            InputOption::VALUE_REQUIRED,
            'mysql connection max life time'
        );

        $this->addOption(
            'size',
            null,
            InputOption::VALUE_REQUIRED,
            'mysql pool size'
        );

        $this->addOption(
            'wait',
            null,
            InputOption::VALUE_REQUIRED,
            'wait mysql connection timeout'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {

        $file = $this->getFile($output);
        if (!$file) {
            return Command::FAILURE;
        }

        $life = $input->getOption("life");
        $size = $input->getOption("size");
        $wait = $input->getOption("wait");

        $host = App::config("database")->get("db.drivers.default.host");
        $port = App::config("database")->get("db.drivers.default.port");
        $socket = App::config("database")->get("db.drivers.default.unix_socket");
        if (!$socket) {
            $output->writeln("<error>Set the unix_socket parameter</>");
            return Command::FAILURE;
        }

        $args = " --host=$host --port=$port --socket=$socket";
        if ($size) {
            $args .= " --size=$size";
        }
        if ($life) {
            $args .= " --life=$life";
        }
        if ($wait) {
            $args .= " --wait=$wait";
        }

        $p = Process::fromShellCommandline($file . $args, null, []);
        $p->setWorkingDirectory(getcwd());
        $p->setTimeout(null);
        $p->run(function ($type, $out) use ($output) {
            $output->write($out);
        });

        return Command::SUCCESS;
    }


    protected function getFile(OutputInterface $output): ?string
    {

        $arch = php_uname('m');
        $os = php_uname('s');

        $binNames = [];
        if (str_contains($os, 'Linux')) {
            $binNames[] = 'linux';
        } elseif (str_contains($os, 'Darwin')) {
            $binNames[] = 'darwin';
        } else {
            $output->writeln("<error>This operating system is not supported</>");
            return null;
        }

        if (str_contains($arch, 'arm')) {
            $binNames[] = 'arm64';
        } elseif (str_contains($arch, 'x86_64') || str_contains($arch, 'amd64')) {
            $binNames[] = 'amd64';
        } else {
            $output->writeln("<error>This cpu architecture is not supported</>");
            return null;
        }
        $binFile = __DIR__ . "/bin/" . implode('-', $binNames);

        if (!file_exists($binFile)) {
            $output->writeln("<error>This system environment is not supported</>");
            return null;
        }
        return $binFile;
    }
}