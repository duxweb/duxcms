<?php

namespace Dux\Database\Redis;

class PredisAdapter
{
    protected \Predis\ClientInterface $client;

    public function __construct($options = []) {
        $this->client = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => $options['host'] ?? '127.0.0.1',
            'port'   => $options['port'] ?? 6379,
            'timeout' => (float)$options['timeout'],
            'prefix' => $options['optPrefix'],
            'password' => $options['password'],
            'database' => $options['database'] ?: 0,
        ]);
    }

    public function getClient(): \Predis\ClientInterface
    {
        return $this->client;
    }
}