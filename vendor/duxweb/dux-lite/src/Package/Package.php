<?php

namespace Dux\Package;

use Composer\Semver\Semver;
use Dux\Handlers\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Nette\Utils\FileSystem;
use Noodlehaus\Config;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZipArchive;

class Package
{
    public static string $url = 'http://dux.plus';

    public static function downloadPackages(OutputInterface $output, Collection $packages, Collection $dependencies, Collection $apps, Collection $composers, Collection $node, Collection $files, array $data): void
    {
        foreach ($data as $item) {
            [$config, $appFiles] = self::download($output, $item);
            $key = $packages->search(function ($vo) use ($item) {
                return $item['name'] == $vo['name'];
            });
            if ($key !== false) {
                $packages->forget($key);
            }
            $packages->add($config);
            $dependencies->put($item['name'], $item['ver']);
            $apps->add($item['app']);

            foreach ($appFiles as $source => $target) {
                $files->put($source, $target);
            }
            $phpDependencies = $config['phpDependencies'] ?: [];
            foreach ($phpDependencies as $key => $vo) {
                if ($composers->has($key)) {
                    continue;
                }
                $composers->put($key, $vo);
            }
            $jsDependencies = $config['jsDependencies'] ?: [];
            foreach ($jsDependencies as $key => $vo) {
                if ($node->has($key)) {
                    continue;
                }
                $node->put($key, $vo);
            }
        }
    }

    public static function download(OutputInterface $output, array $data): array
    {
        $client = new Client();
        $packageDir = data_path('package');
        $packageFile = $packageDir . '/' . $data['md5'] . '.zip';

        $packageFileDir = data_path('package/' . $data['md5']);
        $appDir = $packageFileDir . '/app';
        $jsDir = $packageFileDir . '/js';
        $appConfigFile = $appDir . '/app.json';
        $configFile = $packageFileDir . '/config.yaml';

        if (!$data['app']) {
            throw new Exception('App name not found');
        }

        FileSystem::createDir($packageDir);
        FileSystem::createDir($packageFileDir);

        $progressBar = new ProgressBar($output);
        $progressBar->setFormat('Download %message%: %current%/%max% [%bar%] %percent:3s%%');
        $progressBar->setMessage($data['name']);
        $progressBar->start();
        $response = $client->get($data['url'], [
            'sink' => $packageFile,
            'progress' => function (
                $downloadTotal,
                $downloadedBytes,
                $uploadTotal,
                $uploadedBytes
            ) use ($output, $data, $progressBar) {
                $progressBar->setMaxSteps($downloadTotal);
                $progressBar->setProgress($downloadedBytes);
            },
        ]);

        $output->writeln('');

        if ($response->getStatusCode() != 200) {
            throw new Exception('Failed to download application');
        }

        $fileMd5 = md5_file($packageFile);
        if ($data['md5'] != $fileMd5) {
            throw new Exception('File verification failed');
        }

        $zip = new ZipArchive;
        $res = $zip->open($packageFile);
        if (!$res) {
            throw new Exception('File cannot be opened');
        }
        if (!$zip->extractTo($packageFileDir)) {
            throw new Exception('Corrupted file decompression failure');
        }

        $config = self::getJson($appConfigFile);
        $config['app'] = $data['app'];

        $copyMaps = [
            $appDir => app_path(ucfirst($data['app'])),
        ];
        if (is_dir($jsDir)) {
            $copyMaps[$jsDir] = base_path('web/src/pages/' . $data['app']);
        }
        if (is_file($configFile)) {
            $copyMaps[$configFile] = config_path($data['app'] . '.yaml');
        }

        return [
            $config,
            $copyMaps
        ];
    }

    public static function app(string $token, string $app): array
    {
        return self::request('get', '/v/package/version/app', [
            'query' => [
                'type' => 'duxLite',
                'app' => $app,
                'download' => true
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token
            ],
        ]);
    }

    public static function query(string $token, array $queryData): array
    {
        return self::request('post', '/v/package/version/query', [
            'query' => [
                'type' => 'duxLite',
                'download' => true
            ],
            'json' => $queryData,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token
            ],
        ]);
    }

    public static function composer(OutputInterface $output, array $maps, $remove = false): void
    {
        if (!$maps) {
            return;
        }

        $file = base_path('composer.json');

        if (!is_file($file)) {
            throw new Exception('composer.json file does not exist');
        }

        $fileData = self::getJson($file);
        $require = $fileData['require'];
        if (!$require) {
            throw new Exception('composer require empty');
        }

        if (!$remove) {
            foreach ($maps as $key => $vo) {
                $require[$key] = $vo;
            }
        } else {
            foreach ($maps as $vo) {
                if (!isset($require[$vo])) {
                    continue;
                }
                unset($require[$vo]);
            }
        }

        $fileData['require'] = $require;

        self::saveJson($file, $fileData);
    }

    public static function node(OutputInterface $output, array $maps, $remove = false): void
    {
        if (!$maps) {
            return;
        }

        $file = base_path('web/package.json');
        if (!is_file($file)) {
            throw new Exception('package.json file does not exist');
        }

        $fileData = self::getJson($file);
        $dependencies = $fileData['dependencies'];
        if (!$dependencies) {
            throw new Exception('package require empty');
        }

        if (!$remove) {
            foreach ($maps as $key => $vo) {
                $dependencies[$key] = $vo;
            }
        } else {
            foreach ($maps as $key => $vo) {
                if (!isset($dependencies[$vo])) {
                    continue;
                }
                unset($dependencies[$vo]);
            }
        }

        $fileData['dependencies'] = $dependencies;

        self::saveJson($file, $fileData);
    }

    public static function copy(OutputInterface $output, array $maps): void
    {
        $output->writeln('<info>Copy target files:</info>');
        foreach ($maps as $source => $target) {
            $output->writeln(' - <info>' . str_replace(base_path(), '', $target) . '</info>');
            FileSystem::copy($source, $target);
        }
    }

    public static function del(OutputInterface $output, array $maps): void
    {
        $output->writeln('<info>Delete target files:</info>');
        foreach ($maps as $path) {
            $output->writeln(' - <info>' . str_replace(base_path(), '', $path) . '</info>');
            FileSystem::delete($path);
        }
    }

    public static function saveConfig(OutputInterface $output, array $apps, bool $remove = false): void
    {
        if (!$apps) {
            return;
        }
        $configFile = config_path('app.yaml');
        $conf = Config::load($configFile);
        $registers = $conf->get("registers", []);
        if (!$remove) {
            foreach ($apps as $key => $app) {
                $app = ucfirst($app);
                $name = "\\App\\$app\\App";
                if (in_array($name, (array)$registers)) {
                    continue;
                }
                $registers[] = $name;
            }
        } else {
            foreach ($registers as $key => $vo) {
                foreach ($apps as $app) {
                    $app = ucfirst($app);
                    $name = "\\App\\$app\\App";
                    if ($name == $vo) {
                        unset($registers[$key]);
                    }
                }
            }
        }

        $output->writeln('Configuring Application Injection');
        $conf->set("registers", $registers);
        $conf->toFile($configFile);
    }

    public static function getJson(string $file): array
    {
        $name = str_replace(base_path(), '', $file);
        $content = file_get_contents($file);
        if ($content === false) {
            throw new Exception('Failed to read file ' . $name);
        }
        return json_decode((string)$content, true) ?: [];
    }

    public static function saveJson(string $file, array $data): void
    {
        $name = str_replace(base_path(), '', $file);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!file_put_contents($file, $json)) {
            throw new Exception($name . ' No permission to edit');
        }
    }

    public static function installOther(Application $application, OutputInterface $output, bool $build = false): void
    {
        $output->writeln('<info>Composer installation</info>');
        $childInput = new ArrayInput([
            'command' => 'package:composer',
            'cmd' => 'update',
        ]);
        try {
            $application->find('package:composer')->run($childInput, $output);
        } catch (\Exception $e) {
            $output->writeln('<fg=yellow>' . $e->getMessage() . '</>');
        }

        $output->writeln('<info>Yarn installation</info>');
        $childInput = new ArrayInput([
            'command' => 'package:yarn',
            'cmd' => 'install',
        ]);
        try {
            $application->find('package:yarn')->run($childInput, $output);
        } catch (\Exception $e) {
            $output->writeln('<fg=yellow>' . $e->getMessage() . '</>');
        }

        if ($build) {
            $output->writeln('<info>Yarn build</info>');
            $childInput = new ArrayInput([
                'command' => 'package:yarn',
                'cmd' => 'build',
            ]);
            try {
                $application->find('package:yarn')->run($childInput, $output);
            } catch (\Exception $e) {
                $output->writeln('<fg=yellow>' . $e->getMessage() . '</>');
            }
        }

    }

    public static function getKey(): string
    {
        $keyFile = data_path('cloud.key');
        if (!is_file($keyFile)) {
            return '';
        }
        $content = FileSystem::read($keyFile);
        $key = '';
        try {
            $key = decryption($content);
        }catch (\Exception $e) {}
        return $key ?: '';
    }

    public static function request(string $method, string $path, array $params): array
    {
        $keyFile = data_path('cloud.key');
        $client = new Client();
        try {
            $response = $client->request($method, self::$url . $path, $params);
            $content = $response->getBody()?->getContents();
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $content = $response?->getBody()?->getContents();
        }
        if ($response->getStatusCode() == 401) {
            FileSystem::delete($keyFile);
            throw new Exception('[CLOUD] Wrong user token');
        }
        $responseData = json_decode($content ?: '', true);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('[CLOUD] ' . $response->getStatusCode() . ' ' . ($responseData['message'] ?: 'Server connection failed'));
        }

        return $responseData['data'] ?: [];
    }

    public static function auth(HelperInterface $helper, InputInterface $input, OutputInterface $output): string|int
    {
        $auth = Package::getKey();
        if (!$auth) {
            $question = new Question('Please enter username: ');
            $username = $helper->ask($input, $output, $question);
            if (!$username) {
                $output->writeln('<error>Username not entered<error>');
                return Command::FAILURE;
            }

            $question = new Question('Please enter password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $question);
            if (!$password) {
                $output->writeln('<error>Password not entered<error>');
                return Command::FAILURE;
            }

            $auth = self::login($username, $password);
        }
        return $auth;
    }

    public static function login(string $username, string $password): string
    {
        $keyFile = data_path('cloud.key');
        $data = self::request('POST', '/v/package/auth/token', [
            'json' => [
                'username' => $username,
                'password' => $password
            ]
        ]);
        $auth = $data['token'];
        $keyContent = encryption($data['token']);
        FileSystem::write($keyFile, $keyContent);
        return $auth;
    }

    public static function updateAppVersion($info): void
    {
        $configFile = base_path('app.json');
        $appJson = [];
        if (is_file($configFile)) {
            $appJson = Package::getJson($configFile);
        }
        $apps = $appJson['apps'] ?: [];

        foreach ($info['apps'] as $vo) {
            $app = $vo['name'];
            unset($apps[$app]);
            $apps[$app] = $vo['time'];
        }

        $appJson['apps'] = $apps;
        Package::saveJson($configFile, $appJson);
    }

}