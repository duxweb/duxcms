<?php

declare(strict_types=1);


namespace Dux;

use Clockwork\Authentication\NullAuthenticator;
use Clockwork\Clockwork;
use Clockwork\Storage\FileStorage;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Dotenv\Dotenv;
use Dux\Api\ApiEvent;
use Dux\App\AppExtend;
use Dux\Auth\AuthService;
use Dux\Config\Yaml;
use Dux\Database\Db;
use Dux\Database\Migrate;
use Dux\Event\Event;
use Dux\Handlers\Exception;
use Dux\Lock\Lock;
use Dux\Logs\LogHandler;
use Dux\Queue\Queue;
use Dux\Scheduler\Scheduler;
use Dux\Storage\Storage;
use Dux\Validator\Data;
use Dux\Validator\Validator;
use Dux\View\View;
use GeoIp2\Database\Reader;
use Illuminate\Database\Capsule\Manager;
use Ip2Region;
use Latte\Engine;
use League\Flysystem\Filesystem;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Monolog\Level;
use Monolog\Logger;
use Noodlehaus\Config;
use Phpfastcache\Helper\Psr16Adapter;
use Predis\Client;
use Redis;
use ReflectionClass;
use Slim\App as SlimApp;
use Symfony\Component\Console\Application;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use XdbSearcher;

class App
{
    public static string $basePath;
    public static string $configPath;
    public static string $dataPath;
    public static string $publicPath;
    public static string $appPath;
    public static Bootstrap $bootstrap;
    public static Container $di;
    public static array $config;
    public static array $registerApp = [];

    /**
     * create
     * @param string $basePath
     * @return Bootstrap
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function create(string $basePath, string $lang = 'en'): Bootstrap
    {
        self::$basePath = $basePath;
        self::$configPath = $basePath . '/config';
        self::$dataPath = $basePath . '/data';
        self::$publicPath = $basePath . '/public';
        self::$appPath = $basePath . '/app';

        self::$di = new Container();
        self::$di->set('language', $lang);

        $app = new Bootstrap();
        $app->loadFunc();
        $app->loadConfig();
        $app->loadWeb(self::$di);
        $app->loadCache();
        $app->loadView();
        $app->loadScheduler();
        $app->loadDb();
        $app->loadApp();
        $app->loadRoute();
        self::$bootstrap = $app;
        return $app;
    }

    public static function createCli(string $basePath, string $lang = 'en'): Bootstrap
    {
        $app = self::create($basePath, $lang);
        $app->loadCommand();
        return $app;
    }

    /**
     * @return SlimApp
     */
    public static function app(): SlimApp
    {
        return self::$bootstrap->web;
    }

    /**
     * 注册应用
     * @param array $class
     * @return void
     */
    public static function registerApp(array $class): void
    {
        foreach ($class as $vo) {
            if (!$vo instanceof AppExtend) {
                throw new Exception("The application $vo could not be registered");
            }
            self::$registerApp[] = $vo;
        }
    }

    /**
     * config
     * @source noodlehaus/config
     * @param string $name
     * @return Config
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function config(string $name): Config
    {
        if (self::$di->has("config." . $name)) {
            return self::$di->get("config." . $name);
        }

        $dotenv = Dotenv::createImmutable(self::$basePath);
        $dotenv->safeLoad();

        $file = App::$configPath . "/$name.dev.yaml";
        if (!is_file($file)) {
            $file = App::$configPath . "/$name.yaml";
        }
        $config = new Config($file, new Yaml());
        self::$di->set("config." . $name, $config);
        return $config;
    }

    /**
     * cache
     * @source PHPSocialNetwork/phpfastcache
     * @return Psr16Adapter
     */
    public static function cache(): Psr16Adapter
    {
        return self::$bootstrap->cache;
    }

    /**
     * event
     * @return Event
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function event(): Event
    {
        if (self::$di->has("events")) {
            return self::$di->get("events");
        }
        $event = new Event();
        self::di()->set(
            "events",
            $event
        );
        return $event;
    }

    /**
     * di
     * @return Container
     */
    public static function di(): Container
    {
        return self::$di;
    }

    /**
     * command
     * @return Application
     */
    public static function command(): Application
    {
        return self::$bootstrap->command;
    }

    /**
     * getDebug
     * @return bool
     */
    public static function getDebug(): bool
    {
        return self::$bootstrap->debug;
    }

    /**
     * validator
     * @source https://github.com/vlucas/valitron
     * @param array $data data array
     * @param array $rules ["name", "rule", "message"]
     * @return Data
     */
    public static function validator(array $data, array $rules): Data
    {
        return Validator::parser($data, $rules);
    }

    /**
     * database
     * @source illuminate/database
     * @return Manager
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function db(): Manager
    {
        if (!self::$di->has("db")) {
            self::di()->set(
                "db",
                Db::init(App::config("database")->get("db.drivers"))
            );
        }
        return self::$di->get("db");
    }

    /**
     * dbMigrate
     * @return Migrate
     */
    public static function dbMigrate(): Migrate
    {
        if (!self::$di->has("db.migrate")) {
            self::$di->set(
                "db.migrate",
                new Migrate()
            );
        }
        return self::$di->get("db.migrate");
    }

    /**
     * log
     * @source Seldaek/monolog
     * @param string $app
     * @return Logger
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function log(string $app = "default"): Logger
    {
        if (!self::$di->has("logger." . $app)) {
            self::$di->set(
                "logger." . $app,
                LogHandler::init($app, Level::Debug)
            );
        }
        return self::$di->get("logger." . $app);
    }

    /**
     * queue
     * @param string $type
     * @return Queue
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function queue(string $type = ""): Queue
    {
        if (!$type) {
            $type = self::config("queue")->get("type");
        }
        if (!self::$di->has("queue." . $type)) {
            self::$di->set(
                "queue." . $type,
                new Queue($type)
            );
        }
        return self::$di->get("queue." . $type);
    }

    /**
     * view
     * @param string $name
     * @return Engine
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function view(string $name): Engine
    {
        if (!self::$di->has("view." . $name)) {
            self::$di->set(
                "view." . $name,
                View::init($name)
            );
        }
        return self::$di->get("view." . $name);
    }

    /**
     * storage
     * @param string $type
     * @return Filesystem
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function storage(string $type = ""): Filesystem
    {
        if (!$type) {
            $type = self::config("storage")->get("type");
        }
        if (!self::$di->has("storage." . $type)) {
            $config = self::config("storage")->get("drivers." . $type);
            $storageType = $config["type"];
            unset($config["type"]);
            self::$di->set(
                "storage." . $type,
                Storage::init($storageType, $config)
            );
        }
        return self::$di->get("storage." . $type);
    }

    /**
     * @param string $type
     * @return LockFactory
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function lock(string $type = "semaphore"): LockFactory
    {
        if (!self::$di->has("lock." . $type)) {
            self::$di->set(
                "lock." . $type,
                Lock::init($type)
            );
        }
        return self::$di->get("lock." . $type);
    }

    /**
     * menu
     * @param string $name
     * @return Menu\Menu
     */
    public static function menu(string $name): Menu\Menu
    {
        return self::$bootstrap->getMenu()->get($name);
    }

    /**
     * permission
     * @param string $name
     * @return Permission\Permission
     */
    public static function permission(string $name): Permission\Permission
    {
        return self::$bootstrap->getPermission()->get($name);
    }

    /**
     * redis
     * @param int $database
     * @param string $name
     * @return Client|\Redis
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function redis($database = 0, string $name = "default"): \Predis\ClientInterface|\Redis
    {
        if (!self::$di->has("redis." . $name)) {
            $config = self::config("database")->get("redis.drivers." . $name);
            $redis = (new Database\Redis($config))->connect();
            self::$di->set(
                "redis." . $name,
                $redis
            );
        }
        $redis = self::$di->get("redis." . $name);
        $redis->select($database);
        return $redis;
    }

    /**
     * clock
     * @param null $message
     * @return Clockwork|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function clock($message = null): ?Clockwork
    {
        if (!self::config('use')->get('clock')) {
            return null;
        }
        if (!self::$di->has("clock")) {
            $clockwork = new Clockwork();
            $clockwork->storage(new FileStorage(App::$dataPath . '/clockwork'));
            $clockwork->authenticator(new NullAuthenticator);
            $clockwork->log();
            self::$di->set("clock", $clockwork);
            return $clockwork;
        }
        $clock = self::$di->get("clock");
        if (isset($message)) {
            $clock->log('debug', $message);
        }
        return $clock;
    }

    /**
     * Auth
     * @param string $app
     * @return AuthService
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function auth(string $app = ""): AuthService
    {
        if (!self::$di->has("auth.$app")) {
            self::$di->set(
                "auth.$app",
                new AuthService($app)
            );
        }
        return self::$di->get("auth.$app");
    }

    /**
     * scheduler
     * @return Scheduler
     */
    public static function scheduler(): Scheduler
    {
        return self::$bootstrap->scheduler;
    }

    /**
     * translator
     * @return Translator
     * @throws DependencyException|NotFoundException
     */
    public static function trans(): Translator
    {
        if (!self::$di->has("trans")) {
            $lang = self::$di->get('language') ?: 'en-US';
            $translator = new Translator($lang);
            $translator->addLoader('yaml', new YamlFileLoader());
            $translator->addResource('yaml', __DIR__ . '/Langs/common.en-US.yaml', 'en-US', 'common');
            $translator->addResource('yaml', __DIR__ . '/Langs/common.zh-CN.yaml', 'zh-CN', 'common');
            $translator->addResource('yaml', __DIR__ . '/Langs/common.zh-TW.yaml', 'zh-TW', 'common');
            $translator->addResource('yaml', __DIR__ . '/Langs/common.ja-JP.yaml', 'ja-JP', 'common');
            $translator->addResource('yaml', __DIR__ . '/Langs/common.ko-KR.yaml', 'ko-KR', 'common');
            $translator->addResource('yaml', __DIR__ . '/Langs/common.ru-RU.yaml', 'ru-RU', 'common');
            self::$di->set(
                "trans",
                $translator
            );
        }
        return self::$di->get("trans");
    }

    public static function transAutoRegister(string $appInitClass): void
    {
        $reflection = new ReflectionClass($appInitClass);
        $filePath = $reflection->getFileName();
        $dirPath = dirname($filePath) . "/Langs";
        $files = glob($dirPath . "/*.*.yaml");
        if (!$files) {
            return;
        }
        foreach ($files as $file) {
            $names = explode('.', basename($file, '.yaml'), 2);
            [$name, $lang] = $names;
            self::trans()->addResource('yaml', $file, $lang, $name);
        }
    }

    public static function geo(): XdbSearcher|null
    {
        if (!self::$di->has("geo")) {
            $db = self::config("geo")->get("db");
            $ip2region = is_file($db) ? XdbSearcher::newWithFileOnly($db) : null;
            self::$di->set(
                "geo",
                $ip2region
            );
        }
        return self::$di->get("geo");
    }

    /**
     * api事件触发
     * @param $className
     * @return ApiEvent
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function apiEvent($className): ApiEvent {
        $name = "api.event.$className";
        if (!self::di()->has($name)) {
            $event = new ApiEvent();
            self::event()->dispatch($event,"api.$className");
            self::di()->set($name, $event);
            return $event;
        }
        return self::di()->get($name);
    }
}