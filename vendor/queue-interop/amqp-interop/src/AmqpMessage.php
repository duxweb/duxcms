<?php
declare(strict_types=1);

namespace Interop\Amqp;

use Interop\Queue\Message;

interface AmqpMessage extends Message
{
    const DELIVERY_MODE_NON_PERSISTENT = 1;
    const DELIVERY_MODE_PERSISTENT = 2;

    const FLAG_NOPARAM = 0;
    const FLAG_MANDATORY = 1;
    const FLAG_IMMEDIATE = 2;

    public function setContentType(string $type = null): void;

    public function getContentType(): ?string;

    public function setContentEncoding(string $encoding = null): void;

    public function getContentEncoding(): ?string;

    public function setDeliveryMode(int $deliveryMode = null): void;

    public function getDeliveryMode(): ?int;

    public function setPriority(int $priority = null): void;

    public function getPriority(): ?int;

    public function setExpiration(int $expiration = null): void;

    public function getExpiration(): ?int;

    public function setDeliveryTag(int $deliveryTag = null): void;

    /**
     * https://www.rabbitmq.com/amqp-0-9-1-reference.html#domain.delivery-tag
     */
    public function getDeliveryTag(): ?int;

    public function getConsumerTag(): ?string;

    public function setConsumerTag(string $consumerTag = null): void;

    public function clearFlags(): void;

    public function addFlag(int $flag): void;

    public function getFlags(): int;

    public function setFlags(int $flags): void;

    public function getRoutingKey(): ?string ;

    public function setRoutingKey(string $routingKey = null): void;
}
