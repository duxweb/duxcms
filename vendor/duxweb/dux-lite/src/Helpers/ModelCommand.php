<?php
declare(strict_types=1);

namespace Dux\Helpers;

use Dux\App;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Noodlehaus\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class ModelCommand extends Command {

    protected static $defaultName = 'generate:model';
    protected static $defaultDescription = 'Create an application model';

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
        if (!is_dir($dir)) {
            return $this->error($output, 'The application does not exist');
        }
        $helper = $this->getHelper('question');

        $question = new Question("Please enter a model name: ", false);

        $modelName = $helper->ask($input, $output, $question);
        if (!$modelName) {
            return $this->error($output, "The model name is not entered");
        }
        $modelName = ucwords($modelName);
        $modelDir = "$dir/Models";
        if (is_file("$modelDir/$modelName")) {
            return $this->error($output, "The model already exists");
        }

        $file = new \Nette\PhpGenerator\PhpFile;
        $file->setStrictTypes();
        $namespace = $file->addNamespace("App\\$name\\Models");
        $namespace->addUse(\Dux\Database\Attribute\AutoMigrate::class);
        $namespace->addUse(\Illuminate\Database\Schema\Blueprint::class);
        $namespace->addUse(Connection::class);
        $class = $namespace->addClass($modelName);
        $class->addAttribute(\Dux\Database\Attribute\AutoMigrate::class);
        $class->setExtends(\Dux\Database\Model::class);
        $class->addProperty("table", $this->ccFormat($modelName));
        $method = $class->addMethod("migration")->setBody(implode("\n", [
            '$table->id();',
            '$table->timestamps();',
        ]));
        $method->addParameter("table")->setType(Blueprint::class);

        $method = $class->addMethod("seed");
        $method->addParameter("db")->setType(Connection::class);

        FileSystem::write("$modelDir/$modelName.php", (string) $file);

        $output->writeln("<info>Generate model successfully</info>");
        return Command::SUCCESS;
    }

    public function error(OutputInterface $output, string $message): int {
        $output->writeln("<error>$$message</error>");
        return Command::FAILURE;
    }

    private function ccFormat($name): string
    {
        $temp_array = array();
        for($i=0;$i<strlen($name);$i++){
            $ascii_code = ord($name[$i]);
            if($ascii_code >= 65 && $ascii_code <= 90){
                if($i == 0){
                    $temp_array[] = chr($ascii_code + 32);
                }else{
                    $temp_array[] = '_'.chr($ascii_code + 32);
                }
            }else{
                $temp_array[] = $name[$i];
            }
        }
        return implode('',$temp_array);
    }

}