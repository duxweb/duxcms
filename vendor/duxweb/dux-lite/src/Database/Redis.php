<?php
declare(strict_types=1);

namespace Dux\Database;

use Dux\Database\Redis\PhpRedisAdapter;
use Dux\Database\Redis\PredisAdapter;

class Redis
{
    private PhpRedisAdapter|PredisAdapter $client;

    public function __construct(public array $config)
    {
        if (extension_loaded('redis')) {
            $this->client = new PhpRedisAdapter($config);
        } else {
            $this->client = new PredisAdapter($config);
        }
    }

    public function connect(): \Predis\ClientInterface|\Redis
    {
        return $this->client->getClient();
    }

}