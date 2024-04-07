<?php
declare(strict_types=1);

namespace Dux\Cache;

use Dux\App;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Drivers\Redis\Config as RedisConfig;
use Phpfastcache\Drivers\Predis\Config as PredisConfig;
use Phpfastcache\Helper\Psr16Adapter;

class Cache
{

    public static function init(?string $type): Psr16Adapter
    {
        if (!$type || $type === "files") {
            $config = new ConfigurationOption([
                'path' => App::$dataPath . "/cache"
            ]);
        }
        if ($type === "redis") {
            $driver = App::config('cache')->get('driver', 'default');
            $config = App::config('database')->get($type . ".drivers." . $driver);
            if (extension_loaded('redis')) {
                $config = new RedisConfig([
                    'host' => $config['host'],
                    'port' => $config['port'],
                    'timeout' => (int) $config['timeout'],
                    'password' => $config['auth'] ?: '',
                    'database' => (int) $config['database'] ?: 0,
                    'optPrefix' => $config['optPrefix'] ?: '',
                ]);
            } else {
                $config = new PredisConfig([
                    'host' => $config['host'],
                    'port' => $config['port'],
                    'timeout' => (int) $config['timeout'],
                    'password' => $config['auth'] ?: '',
                    'database' => (int) $config['database'] ?: 0,
                    'optPrefix' => $config['optPrefix'] ?: '',
                ]);
            }

        }
        return new Psr16Adapter($type, $config);
    }
}