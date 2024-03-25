<?php
declare(strict_types=1);

namespace Interop\Amqp;

use Interop\Queue\Context;

/**
 * @method AmqpQueue createQueue($queueName)
 * @method AmqpQueue createTemporaryQueue()
 * @method AmqpProducer createProducer
 * @method AmqpConsumer createConsumer(AmqpDestination $destination)
 * @method AmqpTopic createTopic($topicName)
 * @method AmqpMessage createMessage($body = '', array $properties = [], array $headers = [])
 */
interface AmqpContext extends Context
{
    public function declareTopic(AmqpTopic $topic): void;

    public function deleteTopic(AmqpTopic $topic): void;

    /**
     * Returns messages count
     */
    public function declareQueue(AmqpQueue $queue): int;

    public function deleteQueue(AmqpQueue $queue): void;

    public function bind(AmqpBind $bind): void;

    public function unbind(AmqpBind $bind): void;

    public function setQos(int $prefetchSize, int $prefetchCount, bool $global): void;
}
