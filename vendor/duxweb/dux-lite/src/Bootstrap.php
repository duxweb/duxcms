<?php
declare(strict_types=1);

namespace Dux;


use Carbon\Carbon;
use Clockwork\Support\Slim\ClockworkMiddleware;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Dux\Api\ApiEvent;
use Dux\App\Attribute;
use Dux\Cache\Cache;
use Dux\Command\Command;
use Dux\Config\Config;
use Dux\Database\BackupCommand;
use Dux\Database\ListCommand;
use Dux\Database\MigrateCommand;
use Dux\Database\ProxyCommand;
use Dux\Database\RestoreCommand;
use Dux\Event\EventCommand;
use Dux\Handlers\ErrorHandler;
use Dux\Handlers\ErrorHtmlRenderer;
use Dux\Handlers\ErrorJsonRenderer;
use Dux\Handlers\ErrorPlainRenderer;
use Dux\Handlers\ErrorXmlRenderer;
use Dux\Helpers\AppCommand;
use Dux\Helpers\CtrCommand;
use Dux\Helpers\ManageCommand;
use Dux\Helpers\ModelCommand;
use Dux\Package\ComposerCommand;
use Dux\Package\InstallCommand;
use Dux\Package\PackageInstallCommand;
use Dux\Package\PackageUninstallCommand;
use Dux\Package\PackageUpdateCommand;
use Dux\Package\PushCommand;
use Dux\Package\TransJsonCommand;
use Dux\Package\TransYamlCommand;
use Dux\Package\UninstallCommand;
use Dux\Package\UpdateCommand;
use Dux\Package\YarnCommand;
use Dux\Permission\PermissionCommand;
use Dux\Queue\QueueCommand;
use Dux\Scheduler\SchedulerCommand;
use Dux\Route\RouteCommand;
use Dux\Scheduler\Scheduler;
use Dux\View\View;
use Illuminate\Pagination\Paginator;
use Latte\Engine;
use Phpfastcache\Helper\Psr16Adapter;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App as slimApp;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Symfony\Component\Console\Application;

class Bootstrap
{

    public bool $debug = true;
    public ?slimApp $web = null;
    public ?Application $command = null;
    public Psr16Adapter $cache;
    public array $config;
    public string $exceptionTitle = "Application Error";
    public string $exceptionDesc = "A website error has occurred. Sorry for the temporary inconvenience.";
    public string $exceptionBack = "go back";
    public Engine $view;

    public Route\Register $route;
    public Scheduler $scheduler;
    public Resources\Register $resource;
    public ?Permission\Register $permission = null;
    public ?Menu\Register $menu = null;
    private Container $di;

    /**
     * init
     */
    public function __construct()
    {
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_WARNING);
    }

    public function loadFunc(): void
    {
        require_once "Func/Response.php";
        require_once "Func/Common.php";
    }

    /**
     * loadWeb
     * @param Container $di
     * @return void
     */
    public function loadWeb(Container $di): void
    {
        AppFactory::setContainer($di);
        $this->di = $di;
        $this->web = AppFactory::create();
        $this->resource = new Resources\Register();
        $this->route = new Route\Register();

        $this->web->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($di) {
            if (App::config('use')->get('lang')) {
                $lang = App::config('use')->get('lang');
            } else {
                $lang = $request->getHeaderLine('Accept-Language');
            }
            $di->set('language', $lang);
            return $handler->handle($request);
        });
    }

    /**
     * loadConfig
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function loadConfig(): void
    {
        Config::setValues([
            'base_path' => App::$basePath,
            'app_path' => App::$appPath,
            'data_path' => App::$dataPath,
            'config_path' => App::$configPath,
            'public_path' => App::$publicPath,
        ]);

        Config::setTag('env', function ($key, $default = null) {
            return $_ENV[$key] ?? $default;
        });

        $this->debug = (bool)App::config("use")->get("app.debug");
        $this->exceptionTitle = App::config("use")->get("exception.title", $this->exceptionTitle);
        $this->exceptionDesc = App::config("use")->get("exception.desc", $this->exceptionDesc);
        $this->exceptionBack = App::config("use")->get("exception.back", $this->exceptionBack);

        $timezone = App::config("use")->get("app.timezone", 'PRC');
        date_default_timezone_set($timezone);

        Carbon::setLocale(App::config("use")->get("lang", 'auto'));
    }

    /**
     * loadCache
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function loadCache(): void
    {
        $type = App::config("cache")->get("type");
        $this->cache = Cache::init($type);
    }

    /**
     * loadCommand
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function loadCommand(): void
    {
        $commands = App::config("command")->get("registers", []);
        $commands[] = QueueCommand::class;
        $commands[] = SchedulerCommand::class;
        $commands[] = RouteCommand::class;
        $commands[] = MigrateCommand::class;
        $commands[] = EventCommand::class;
        $commands[] = AppCommand::class;
        $commands[] = ModelCommand::class;
        $commands[] = ManageCommand::class;
        $commands[] = CtrCommand::class;
        $commands[] = App\AppCommand::class;
        $commands[] = PermissionCommand::class;
        $commands[] = ListCommand::class;
        $commands[] = InstallCommand::class;
        $commands[] = UninstallCommand::class;
        $commands[] = PushCommand::class;
        $commands[] = UpdateCommand::class;
        $commands[] = YarnCommand::class;
        $commands[] = ComposerCommand::class;
        $commands[] = TransYamlCommand::class;
        $commands[] = TransJsonCommand::class;
        $commands[] = PackageInstallCommand::class;
        $commands[] = PackageUpdateCommand::class;
        $commands[] = PackageUninstallCommand::class;
        $commands[] = BackupCommand::class;
        $commands[] = RestoreCommand::class;
        $this->command = Command::init($commands);



        // 注册模型迁移
        App::dbMigrate()->registerAttribute();
    }

    /**
     * loadView
     * @return void
     */
    public function loadView(): void
    {
        $this->view = View::init("app");
    }

    /**
     * loadRoute
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function loadRoute(): void
    {
        if (App::config('use')->get('clock')) {
            $this->web->add(new ClockworkMiddleware($this->web, $this->di->get('clock')));
        }
        // 解析内容
        $this->web->addBodyParsingMiddleware();
        // 注册路由中间件
        $this->web->addRoutingMiddleware();

        // 注册异常处理
        $errorMiddleware = $this->web->addErrorMiddleware($this->debug, true, true);
        $errorHandler = new ErrorHandler($this->web->getCallableResolver(), $this->web->getResponseFactory());
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
        $errorHandler->registerErrorRenderer("application/json", ErrorJsonRenderer::class);
        $errorHandler->registerErrorRenderer("application/xml", ErrorXmlRenderer::class);
        $errorHandler->registerErrorRenderer("text/xml", ErrorXmlRenderer::class);
        $errorHandler->registerErrorRenderer("text/html", ErrorHtmlRenderer::class);
        $errorHandler->registerErrorRenderer("text/plain", ErrorPlainRenderer::class);
        $this->di->set('error', $errorHandler);

        // 跨域处理
        $this->web->options('/{routes:.+}', function ($request, $response, $args) {
            return $response;
        });
        $this->web->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
            $params = $request->getQueryParams();
            Paginator::currentPageResolver(static function ($pageName = 'page') use ($params) {
                $page = $params[$pageName];
                if ((int)$page >= 1) {
                    return $page;
                }
                return 1;
            });
            $response = $handler->handle($request);

            $origin = $request->getHeaderLine('Origin');
            return $response->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, PATCH, DELETE')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Content-MD5, Platform, Content-Date, Authorization, AccessKey')
                ->withHeader('Access-Control-Expose-Methods', '*')
                ->withHeader('Access-Control-Expose-Headers', '*')
                ->withHeader('Access-Control-Allow-Credentials', 'true');
        });

        $cache = (bool)App::config("use")->get("app.cache");
        if ($cache) {
            $routeCollector = $this->web->getRouteCollector();
            $routeCollector->setCacheFile(App::$dataPath . '/cache/route.file');
        }

    }


    public function loadScheduler(): void
    {
        $this->scheduler = new Scheduler();
    }

    public function loadDb(): void
    {
    }

    /**
     * load app
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function loadApp(): void
    {

        $appList = App::config("app")->get("registers", []);
        foreach ($appList as $vo) {
            App::$registerApp[] = $vo;
        }

        // 注解加载
        App::di()->set("attributes", Attribute::load(App::$registerApp));

        // 事件注解加载
        App::event()->registerAttribute();

        // 注册语言包
        foreach ($appList as $vo) {
            App::transAutoRegister($vo);
        }

        // 事件注册
        foreach ($appList as $vo) {
            call_user_func([new $vo, "init"], $this);
        }

        // 资源注册
        foreach ($this->resource->app as $resource) {
            $resource->run($this);
        }

        // 应用注册
        foreach ($appList as $vo) {
            call_user_func([new $vo, "register"], $this);
        }

        // 注解资源注册
        $this->resource->registerAttribute($this);

        // 注解路由注册
        $this->route->registerAttribute($this);

        // 普通路由注册
        foreach ($this->route->app as $route) {
            $route->run($this->web);
        }

        // 公共路由
        $this->web->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
            throw new HttpNotFoundException($request);
        });

        // 应用启动
        foreach ($appList as $vo) {
            call_user_func([new $vo, "boot"], $this);
        }
    }

    public function run(): void
    {
        if ($this->command) {
            $this->command->run();
        } else {
            $this->web->run();
        }
    }

    public function getEvent(): Event\Event
    {
        return App::event();
    }

    public function getRoute(): Route\Register
    {
        return $this->route;
    }

    public function getResource(): Resources\Register
    {
        return $this->resource;
    }

    public function getPermission(): Permission\Register
    {
        if (!$this->permission) {
            $this->permission = new Permission\Register();
        }
        return $this->permission;
    }

    public function getMenu(): Menu\Register
    {
        if (!$this->menu) {
            $this->menu = new Menu\Register();
        }
        return $this->menu;
    }

}