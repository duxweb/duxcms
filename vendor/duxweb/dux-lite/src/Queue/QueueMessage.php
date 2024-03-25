<?php
declare(strict_types=1);

namespace Dux\Queue;

use Enqueue\Redis\RedisMessage;
use Symfony\Component\Messenger\MessageBus;

class QueueMessage
{

    private \Interop\Queue\Message $message;
    private int|float $delay = 0;

    public function __construct(
        public \Interop\Queue\Context $context,
        public \Interop\Queue\Queue $queue,
        public string     $class,
        public string     $method,
        public array      $params = []
    )
    {
        $this->message = $this->context->createMessage(json_encode([
            'class' => $this->class,
            'method' => $this->method,
            'params' => $this->params
        ]));
    }

    public function delay($second = 0): self
    {
        $this->delay = $second * 1000;
        return $this;
    }

    public function send(): void
    {
        $this->context->createProducer()->setDeliveryDelay($this->delay)->send($this->queue, $this->message);
    }
}