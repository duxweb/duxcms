<?php
declare(strict_types=1);

namespace Dux\Helpers;

use Dux\App;
use Noodlehaus\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Nette\Utils\FileSystem;

class AppCommand extends Command {

    protected static $defaultName = 'generate:app';
    protected static $defaultDescription = 'Create an application module';

    protected function configure(): void {
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'please enter the application name'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $name = $input->getArgument('name');
        $name = ucfirst($name);
        $dir = App::$appPath . "/$name";
        if (is_dir($dir)) {
            return $this->error($output, 'The application already exists');
        }

        // App.php
        $file = new \Nette\PhpGenerator\PhpFile;
        $file->setStrictTypes();
        $namespace = $file->addNamespace("App\\" . $name);
        $class = $namespace->addClass("App");
        $class->setExtends(App\AppExtend::class);
        $class->addComment("Application Registration");
        FileSystem::write("$dir/App.php", (string) $file);

        // config
        $configFile = App::$configPath . "/app.yaml";
        $conf = Config::load($configFile);
        $registers = $conf->get("registers", []);
        $registers[] = "\\App\\$name\\App";
        $conf->set("registers", $registers);
        $conf->toFile($configFile);

        $output->writeln("<info>Generate application successfully</info>");
        return Command::SUCCESS;
    }

    public function error(OutputInterface $output, string $message): int {
        $output->writeln("<error>$$message</error>");
        return Command::FAILURE;
    }

}