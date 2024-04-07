<?php

declare(strict_types=1);

namespace App\Cloud\Admin;

use App\System\Models\SystemUser;
use App\System\Service\Config;
use Dux\Handlers\ExceptionBusiness;
use Dux\Package\ComposerCommand;
use Dux\Package\Install;
use Dux\Package\InstallCommand;
use Dux\Package\Package;
use Dux\Package\Uninstall;
use Dux\Package\UninstallCommand;
use Dux\Package\Update;
use Dux\Package\UpdateCommand;
use Dux\Package\YarnCommand;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Nette\Utils\FileSystem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\StreamOutput;

#[Resource(app: 'admin',  route: '/cloud/apps', name: 'cloud.apps', actions: false)]
class Apps
{

    #[Action(methods: 'GET', route: '')]
    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $file = base_path('app.json');
        $config = Package::getJson($file);
        $apps = implode(',', array_keys($config['apps']));
        $list = [];
        try {
            $auth = Package::getKey();
            $info = Package::app($auth, $apps);
            $list = $info['apps'] ?: [];
        }catch (\Exception) {
        }

        if (!$auth) {
            throw new ExceptionBusiness(__('cloud.apps.validator.notLogin', 'manage'));
        }

        foreach ($list as $key => $vo) {
            $list[$key]['local_time'] = $config['apps'][$vo['name']];
            $list[$key]['update'] = $config['apps'][$vo['name']] < $vo['time'];
        }

        return send($response, 'ok', $list, [
            'auth' => $auth ?: []
        ]);
    }

    #[Action(methods: 'POST', route: '')]
    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $username = $data['username'];
        $password = $data['password'];

        if (!$username || !$password) {
            throw new ExceptionBusiness(__('cloud.apps.validator.username', 'manage'));
        }

        Package::login($username, $password);
        return send($response, __('cloud.apps.alert.login', 'manage'));
    }


    #[Action(methods: 'POST', route: '/install')]
    public function install(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $url = $data['url'];
        $build = $data['build'];
        if (!str_contains($url, 'dux://app/')) {
            throw new ExceptionBusiness('应用地址不正确');
        }
        $parse = parse_url($url);
        $name = trim($parse['path'], '/');
        $output = new BufferedOutput();
        $auth = Package::getKey();
        if (!$auth) {
            throw new ExceptionBusiness(__('cloud.apps.validator.notLogin', 'manage'));
        }

        $application = new Application();
        $application->add(new InstallCommand());
        $application->add(new ComposerCommand());
        $application->add(new YarnCommand());

        $buildCmd = !$build ? '' : ' --build=true';
        $input = new StringInput("install $name $buildCmd");
        try {
            $application->find('install')->run($input, $output);
        } catch (\Exception $e) {
            $output->writeln('<fg=yellow>' . $e->getMessage() . '</>');
        }

        return send($response, 'ok', [
            'content' => $output->fetch()
        ]);
    }

    #[Action(methods: 'POST', route: '/update')]
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $name = $data['name'];
        $build = $data['build'];
        $auth = Package::getKey();
        if (!$auth) {
            throw new ExceptionBusiness(__('cloud.apps.validator.notLogin', 'manage'));
        }

        $output = new BufferedOutput();
        $application = new Application();
        $application->add(new UpdateCommand());
        $application->add(new ComposerCommand());
        $application->add(new YarnCommand());

        $buildCmd = !$build ? '' : ' --build=true';
        $input = new StringInput("update $name $buildCmd");
        try {
            $application->find('update')->run($input, $output);
        } catch (\Exception $e) {
            $output->writeln('<fg=yellow>' . $e->getMessage() . '</>');
        }

        return send($response, 'ok', [
            'content' => $output->fetch()
        ]);
    }

    #[Action(methods: 'POST', route: '/delete')]
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody() ?: [];
        $build = $params['build'];
        $name = $params['name'];
        if (!$name) {
            throw new ExceptionBusiness(__('cloud.apps.validator.name', 'manage'));
        }
        if (!$params['password']) {
            throw new ExceptionBusiness(__('cloud.apps.validator.password', 'manage'));
        }

        $auth = $request->getAttribute('auth');
        $userInfo = SystemUser::query()->find($auth['id']);

        if (!password_verify($params['password'], $userInfo->password)) {
            throw new ExceptionBusiness(__('cloud.apps.validator.passwordError', 'manage'));
        }

        if (!$auth) {
            throw new ExceptionBusiness(__('cloud.apps.validator.notLogin', 'manage'));
        }

        $output = new BufferedOutput();
        $application = new Application();
        $application->add(new UninstallCommand());
        $application->add(new ComposerCommand());
        $application->add(new YarnCommand());

        $buildCmd = !$build ? '' : ' --build=true';
        $input = new StringInput("uninstall $name $buildCmd");
        try {
            $application->find('uninstall')->run($input, $output);
        } catch (\Exception $e) {
            $output->writeln('<fg=yellow>' . $e->getMessage() . '</>');
        }

        return send($response, 'ok', [
            'content' => $output->fetch()
        ]);
    }


}
