<?php

namespace App\Install\Web;

use Dux\App;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Output\BufferedOutput;

set_time_limit(120);

#[RouteGroup(app: 'web', pattern: '/install')]
class Install
{

    #[Route(methods: 'GET', pattern: '')]
    public function location(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $response->withStatus(302)->withHeader('Location', '/install/');
    }

    #[Route(methods: 'GET', pattern: '/')]
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = \Dux\App::view('web');
        $data = json_decode(file_get_contents(public_path('/web/.vite/manifest.json')) ?: '', true);
        $vite = App::config('use')->get('vite', []);
        $html = $view->renderToString(dirname(__DIR__) . "/Views/Web/install.html", [
            "title" => App::config('use')->get('app.name'),
            'vite' => [
                'dev' => (bool)$vite['dev'],
                'port' => $vite['port'] ?: 5173,
            ],
            'manifest' => [
                'js' => $data['src/install.tsx']['file'],
                'css' => $data['style.css']['file'],
            ]
        ]);

        return sendText($response, $html);
    }

    #[Route(methods: 'GET', pattern: '/detection')]
    public function detection(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $extData = [
            [
                'name' => 'Bcmath',
                'status' => extension_loaded('bcmath')
            ],
            [
                'name' => 'Fileinfo',
                'status' => extension_loaded('fileinfo')
            ],
            [
                'name' => 'Pdo',
                'status' => extension_loaded('pdo')
            ],
            [
                'name' => 'GD',
                'status' => function_exists('imagecreate')
            ],
        ];

        $status = true;
        foreach ($extData as $vo) {
            if (!$vo['status']) {
                $status = false;
                break;
            }
        }

        $composerLock = json_decode(file_get_contents(base_path('composer.lock')), true);
        $packageName = 'duxweb/dux-lite';

        $packageData = [];
        foreach ($composerLock['packages'] as $package) {
            if ($package['name'] === $packageName) {
                $packageData[] = [
                    'name' => $package['name'],
                    'ver' => $package['version']
                ];
            }
        }

        return send($response, 'ok', [
            'ext' => $extData,
            'packages' => $packageData,
            'status' => $status
        ]);
    }

    #[Route(methods: 'POST', pattern: '/config')]
    public function database(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody();
        $database = $params['database'] ?: [];

        $message = '';
        try {
            $conn = new PDO("mysql:host=" . $database['host'] . ";port=" . $database['port'] . ";dbname=" . $database['name'], $database['username'], $database['password']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            $message = $e->getMessage();
        }

        return send($response, 'ok', [
            'error' => (bool)$message,
            'message' => $message
        ]);
    }

    #[Route(methods: 'POST', pattern: '/complete')]
    public function complete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody();
        $error = false;
        $message = '';
        $useData = $params['use'];
        $dbData = $params['database'];

        $output = new BufferedOutput();
        $db = App::config('database');
        $use = App::config('use');
        $storage = App::config('storage');

        $db->set('db.drivers.default.host', $dbData['host']);
        $db->set('db.drivers.default.database', $dbData['name']);
        $db->set('db.drivers.default.username', $dbData['username']);
        $db->set('db.drivers.default.password', $dbData['password']);
        $db->set('db.drivers.default.port', $dbData['port']);
        $db->toFile(config_path('/database.yaml'));

        $output->writeln('config database success');

        $use->set('app.name', $useData['name']);
        $use->set('app.domain', $useData['domain']);
        $use->set('app.secret', bin2hex(random_bytes(16)));
        $use->set('lang', $useData['lang']);
        $use->toFile(config_path('/use.yaml'));

        $output->writeln('config use success');

        $storage->set('drivers.local.public_url', $useData['domain'] . '/uploads/');
        $storage->toFile(config_path('/storage.yaml'));
        $output->writeln('storage use success');

        App::db()->getDatabaseManager()->connectUsing('default', App::config("database")->get("db.drivers.default"), true);

        App::dbMigrate()->registerAttribute();
        try {
            App::dbMigrate()->migrate($output);
            $output->writeln('sync database success');
            file_put_contents(data_path('/install.lock'), now()->format('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }

        return send($response, 'ok', [
            'error' => $error,
            'message' => $message,
            'logs' => $output->fetch(),
        ]);

    }


}