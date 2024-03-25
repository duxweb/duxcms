<?php
declare(strict_types=1);

namespace Dux\Helpers;

use Dux\App;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Question\Question;

class ManageCommand extends Command {

    protected static $defaultName = 'generate:manage';
    protected static $defaultDescription = 'Create an manage controller';

    protected function configure(): void {
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'please enter the application name'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $appName = $input->getArgument('name');
        $appName = ucfirst($appName);
        $appDir = App::$appPath . "/$appName";
        if (!is_dir($appDir)) {
            return $this->error($output, 'The application does not exist');
        }
        $helper = $this->getHelper('question');

        $question = new Question("Please enter a dir name: ", false);
        $layerName = $helper->ask($input, $output, $question);
        if (!$layerName) {
            return $this->error($output, "The dir name is not entered");
        }
        $layerName = ucwords($layerName);
        $dirPath = "$appDir/$layerName";

        $question = new Question("Please enter a class name: ", false);
        $className = $helper->ask($input, $output, $question);
        if (!$className) {
            return $this->error($output, "The class name is not entered");
        }
        $className = ucwords($className);

        // manage
        $managePath = "$dirPath/$className.php";
        $file = new \Nette\PhpGenerator\PhpFile;
        $file->setStrictTypes();
        $namespace = $file->addNamespace("App\\$appName\\$layerName");
        $namespace->addUse(\Dux\Resources\Action\Resources::class);
        $namespace->addUse(Data::class);
        $namespace->addUse(\Illuminate\Database\Eloquent\Model::class);
        $namespace->addUse(\Dux\Resources\Attribute\Resource::class);
        $namespace->addUse(ServerRequestInterface::class);
        $class = $namespace->addClass($className);

        $name = lcfirst($appName) . "." . lcfirst($className);
        $pattern = "/" . str_replace(".", "/", $name);
        $class->addAttribute(Resource::class, [
            'app' => lcfirst($layerName),
            'route' => $pattern,
            'name' => $name
        ]);
        $class->addProperty("model", "")->setType("string")->setProtected();
        $class->setExtends(\Dux\Resources\Action\Resources::class);

        $method = $class->addMethod("transform")->setReturnType("array")->setBody('return [
    "id" => $item->id,
];')->setPublic();
        $method->addParameter("item")->setType("object");

        $method = $class->addMethod("validator")->setReturnType("array")->setBody('return [
    "name" => ["required", "please enter name"],
];')->setProtected();
        $method->addParameter("data")->setType("array");
        $method->addParameter("request")->setType(ServerRequestInterface::class);
        $method->addParameter("args")->setType("array");

        $method = $class->addMethod("format")->setReturnType("array")->setBody('return [
    "name" => $data->name,
];')->setProtected();
        $method->addParameter("data")->setType(Data::class);
        $method->addParameter("request")->setType(ServerRequestInterface::class);
        $method->addParameter("args")->setType("array");

        FileSystem::write($managePath, (string)$file);

        $output->writeln("<info>Generate manage successfully</info>");
        return Command::SUCCESS;
    }



    public function error(OutputInterface $output, string $message): int {
        $output->writeln("<error>$$message</error>");
        return Command::FAILURE;
    }


}