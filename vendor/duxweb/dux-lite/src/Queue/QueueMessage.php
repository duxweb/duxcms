<?php
declare(strict_types=1);

namespace Dux\Queue;


class QueueMessage
{

    private \Interop\Queue\Message $message;
    private \Interop\Queue\Queue $queue;
    private int|float $delay = 0;


    public function __construct(
        public \Interop\Queue\Context $context,
        public string     $class,
        public string     $method = '',
        public array      $params = [],
        public string $name = '',
    )
    {
        $this->queue = $this->context->createQueue($name);
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