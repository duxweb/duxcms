<?php

namespace Dux\Database\Redis;

class PhpRedisAdapter
{
    protected \Redis $client;

    public function __construct(array $options = []) {
        $this->client = new \Redis();
        $this->client->connect($options["host"] ?? '127.0.0.1', (int)$options["port"] ?? 6379, (float)$options["timeout"]);
        if ($options["password"]) {
            $this->client->auth($options["auth"]);
        }
        $database = (int)$options["database"] ?: 0;
        $this->client->select($database);
        if ($options["optPrefix"]) {
            $this->client->setOption(\Redis::OPT_PREFIX, $options["optPrefix"]);
        }
    }

    public function getClient(): \Redis
    {
        return $this->client;
    }
}