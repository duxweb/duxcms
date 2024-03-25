<?php
declare(strict_types=1);

namespace Dux\Helpers;

use Dux\App;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Dux\Validator\Data;
use Illuminate\Database\Schema\Blueprint;
use Noodlehaus\Config;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class CtrCommand extends Command
{

    protected static $defaultName = 'generate:ctr';
    protected static $defaultDescription = 'Create an application ctr';

    protected function configure(): void
    {
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'please enter the application name'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
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

        $managePath = "$dirPath/$className.php";
        $file = new \Nette\PhpGenerator\PhpFile;
        $file->setStrictTypes();
        $namespace = $file->addNamespace("App\\$appName\\$layerName");
        $namespace->addUse(\Psr\Http\Message\ResponseInterface::class);
        $namespace->addUse(\Psr\Http\Message\ServerRequestInterface::class);
        $namespace->addUse(\Dux\Validator\Validator::class);
        $class = $namespace->addClass($className);

        $class->addAttribute(RouteGroup::class, [
            'app' => 'web',
            'pattern' => lcfirst($appName) . "/" . lcfirst($className)
        ]);

        $method = $class->addMethod("list")->setReturnType(\Psr\Http\Message\ResponseInterface::class)->setBody("
    \$data = Validator::parser([...\$request->getParsedBody(), ...\$args], [
        'field' => ['required', 'Please enter'],
    ]);
    return send(\$response, 'ok');
    ")->setPublic();
        $method->addAttribute(Route::class, [
            'methods' => 'GET',
            'pattern' => ''
        ]);
        $method->addParameter("request")->setType(\Psr\Http\Message\ServerRequestInterface::class);
        $method->addParameter("response")->setType(\Psr\Http\Message\ResponseInterface::class);
        $method->addParameter("args")->setType('array');
        FileSystem::write($managePath, (string)$file);
        $output->writeln("<info>Generate manage successfully</info>");
        return Command::SUCCESS;
    }

    public function error(OutputInterface $output, string $message): int
    {
        $output->writeln("<error>$$message</error>");
        return Command::FAILURE;
    }

}