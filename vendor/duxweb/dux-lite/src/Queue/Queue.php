<?php
declare(strict_types=1);

namespace Dux\Queue;

use Dux\App;
use Enqueue\Consumption\QueueConsumer;
use Enqueue\Redis\RedisConnectionFactory;
use Redis;
use RuntimeException;

class Queue
{
    public Redis $client;

    public array $config = [];

    public array $group = [];
    private RedisConnectionFactory $factory;
    private \Interop\Queue\Context $context;
    private \Interop\Queue\Queue $queue;

    public function __construct(string $type)
    {
        if ($type !== "redis") {
            throw new RuntimeException("Queue type not supported");
        }

        $driver = App::config('queue')->get('driver', 'default');
        $config = App::config('database')->get($type . ".drivers." . $driver);

        $this->factory = new RedisConnectionFactory([
            'host' => $config['host'],
            'port' => $config['port'],
            'password' => $config['auth'],
            'persistent' => $config['persistent'],
            'scheme_extensions' => ['phpredis'],
        ]);

        $this->context = $this->factory->createContext();
        $this->queue = $this->context->createQueue('queue');


    }

    public function add(string $class, string $method = "", array $params = []): QueueMessage
    {
        return new QueueMessage($this->context, $this->queue, $class, $method, $params);
    }

    public function process(): void
    {
        $processor = new QueueProcessor();
        $consumer = new QueueConsumer($this->context);
        $consumer->bind($this->queue, $processor);

        $consumer->consume();

    }

}