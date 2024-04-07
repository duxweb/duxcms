<?php
declare(strict_types=1);

namespace Dux\Queue;

use Dux\App;
use Enqueue\Consumption\QueueConsumer;
use Enqueue\Redis\RedisConnectionFactory;
use Enqueue\AmqpLib\AmqpConnectionFactory;
use RuntimeException;

class Queue
{
    private \Interop\Queue\ConnectionFactory $factory;
    private \Interop\Queue\Context $context;
    private string $prefix = '';

    public function __construct(string $type)
    {
        if ($type !== "redis" && $type !== 'amqp') {
            throw new RuntimeException("Queue type not supported");
        }

        $driver = App::config('queue')->get('driver', 'default');
        $config = App::config('database')->get($type . ".drivers." . $driver);

        if ($type == "redis") {
            $this->factory = new RedisConnectionFactory([
                'host' => $config['host'],
                'port' => $config['port'],
                'password' => $config['auth'],
                'persistent' => $config['persistent'],
                'scheme_extensions' => ['predis'],
                'predis_options' => [
                    'prefix' => $config['optPrefix']
                ]
            ]);
        }
        if ($type == "amqp") {
            $this->factory = new AmqpConnectionFactory([
                'host' => $config['host'],
                'port' => $config['port'],
                'vhost' => $config['vhost'],
                'user' => $config['username'],
                'pass' => $config['password'],
                'persisted' => $config['persisted'],
            ]);
            $this->prefix = $config['prefix'];
        }

        $this->context = $this->factory->createContext();
    }

    public function add(string $class, string $method = "", array $params = [], string $name = 'queue'): QueueMessage
    {
        return new QueueMessage($this->context, $class, $method, $params, $this->prefix . $name);
    }

    public function process(string $name = 'queue'): void
    {
        $processor = new QueueProcessor();
        $consumer = new QueueConsumer($this->context);
        $consumer->bind($this->context->createQueue($this->prefix . $name), $processor);
        $consumer->consume();

    }

}