<?php
declare(strict_types=1);

namespace Dux\Package;

use Dux\Handlers\Exception;
use GuzzleHttp\Client;
use Nette\Utils\FileSystem;
use Noodlehaus\Parser\Json;
use Noodlehaus\Parser\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Translation\Loader\YamlFileLoader;

class TransJsonCommand extends Command
{

    protected static $defaultName = 'trans:json';
    protected static $defaultDescription = 'Automatic translation of back-end language packs';

    protected function configure(): void
    {
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'please enter the app name'
        )
            ->addOption('pack', null, InputOption::VALUE_REQUIRED, 'Language pack name.')
            ->addOption('lang', null, InputOption::VALUE_REQUIRED, 'Source language.');
    }


    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $pack = $input->getOption('pack');
        if ($pack && $pack != 'common') {
            $pack = $pack . '.';
        }
        $lang = $input->getOption('lang');
        $io = new SymfonyStyle($input, $output);

        $langFile = base_path('web/src/pages/' . lcfirst($name) . '/locales/' . $pack . $lang . '.json');
        if (!is_file($langFile)) {
            $io->error('File does not exist');
            return Command::FAILURE;
        }

        $json = new Json();
        $data = $json->parseFile($langFile);

        $helper = $this->getHelper('question');

        $auth = Package::auth($helper, $input, $output);
        if (is_int($auth)) {
            return $auth;
        }

        Trans::main($auth, $lang, $data, file_get_contents($langFile), function ($lang) use ($name, $pack) {
            return base_path('web/src/pages/' . lcfirst($name) . '/locales/' . $pack . $lang . '.json');
        });

        $io->success('Trans Success');
        return Command::SUCCESS;
    }

}