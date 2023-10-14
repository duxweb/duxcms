<?php
declare(strict_types=1);

namespace App\Cms\Command;

use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Nette\Utils\FileSystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CloudCommand extends Command
{

    protected static $defaultName = 'app:push';
    protected static $defaultDescription = 'Release the application version';

    protected function configure(): void
    {
        $this->addArgument(
            'app',
            InputArgument::REQUIRED,
            'please enter the app name'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);
        $app = $input->getArgument('app');
        $configPath = app_path(ucfirst($app) . '/app.json');
        if (!is_file($configPath)) {
            $io->error('Configuration file does not exist');
            return Command::FAILURE;
        }
        $config = json_decode(file_get_contents($configPath), true);
        if (!$config) {
            $io->error('Configuration does not exist');
            return Command::FAILURE;
        }
        if (!$config['name'] || !$config['version']) {
            $io->error('Configuration field "name" or "version" does not exist');
            return Command::FAILURE;
        }

        $output->writeln('current version number: ' . $config['version']);
        $helper = $this->getHelper('question');

        $question = new Question('new version number: ');
        $version = $helper->ask($input, $output, $question);
        if (!$version) {
            $io->error('Version number not entered');
            return Command::FAILURE;
        }

        $question = new Question('Please enter username: ');
        $username = $helper->ask($input, $output, $question);
        if (!$username) {
            $io->error('Username not entered');
            return Command::FAILURE;
        }

        $question = new Question('Please enter password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $question);
        if (!$password) {
            $io->error('password not entered');
            return Command::FAILURE;
        }

        $config['version'] = $version;
        file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $tmpDir = data_path('cloud/app/' . $app);
        $tmpZip = data_path('cloud/app/' . $app . '.zip');
        if (is_dir($tmpDir)) {
            FileSystem::delete($tmpDir);
        }
        if (is_file($tmpZip)) {
            FileSystem::delete($tmpZip);
        }

        $appDir = $tmpDir . '/app';
        $jsDir = $tmpDir . '/js';
        $configDir = $tmpDir . '/config.yaml';
        mkdir($appDir, 0777, true);
        mkdir($jsDir, 0777, true);

        $appSourceDir = app_path(ucfirst($app));
        $jsSourceDir = base_path('web/src/pages/' . lcfirst($app));
        $configSourceFile = config_path(lcfirst($app) . '.yaml');

        FileSystem::copy($appSourceDir, $appDir);
        if (is_dir($jsSourceDir)) {
            FileSystem::copy($jsSourceDir, $jsDir);
        }
        if (is_file($configSourceFile)) {
            FileSystem::copy($configSourceFile, $configDir);
        }

        $zipFile = data_path('cloud/app/' . $app . '.zip');
        $fileStream = fopen($zipFile, 'w+b');

        $zip = new \ZipStream\ZipStream(
            outputStream: $fileStream,
            defaultEnableZeroHeader: false
        );
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tmpDir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($tmpDir) + 1);

                // 将文件添加到 zip 文件中
                $zip->addFileFromPath($relativePath, $filePath);
            }
        }
        $zip->finish();
        fclose($fileStream);

        $fileSize = filesize($zipFile);
        $progressBar = new ProgressBar($output, $fileSize);
        $progressBar->setFormat('Upload Code: %current%/%max% [%bar%] %percent:3s%%');
        $progressBar->start();

        $client = new Client();

        try {
            $response = $client->post('http://cloud.test/v/version/manage/' . $config['name'], [
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'auth' => [$username, $password],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($zipFile, 'r')
                    ],
                    [
                        'name' => 'type',
                        'contents' => 'php'
                    ],
                    [
                        'name' => 'md5',
                        'contents' => md5_file($zipFile)
                    ],
                    [
                        'name' => 'app',
                        'contents' => ucfirst($app)
                    ],
                ],
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) use ($progressBar) {
                    $uploadedBytes = $stats->getHandlerStats()['uploaded_bytes'] ?? 0;
                    $progressBar->setProgress($uploadedBytes);
                }
            ]);
            $content = $response->getBody()?->getContents();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse();
            $content = $response->getBody()?->getContents();
        } finally {
            $progressBar->finish();
            if (is_dir($tmpDir)) {
                FileSystem::delete($tmpDir);
            }
            if (is_file($tmpZip)) {
                FileSystem::delete($tmpZip);
            }
        }
        if ($response->getStatusCode() == 401) {
            $io->error('[CLOUD] Wrong username and password');
            return Command::FAILURE;
        }
        $responseData = json_decode($content ?: '', true);
        if ($response->getStatusCode() !== 200) {
            $io->warning('[CLOUD] ' . $responseData['message'] ?: 'Server connection failed');
            return Command::FAILURE;
        }

        $io->success('Publish Application Success');
        return Command::SUCCESS;
    }
}