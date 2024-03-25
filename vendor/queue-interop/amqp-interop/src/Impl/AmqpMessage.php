<?php
declare(strict_types=1);

namespace Interop\Amqp\Impl;

use Interop\Amqp\AmqpMessage as InteropAmqpMessage;

final class AmqpMessage implements InteropAmqpMessage
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var array
     */
    private $properties;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var int|null
     */
    private $deliveryTag;

    /**
     * @var string|null
     */
    private $consumerTag;

    /**
     * @var bool
     */
    private $redelivered;

    /**
     * @var int
     */
    private $flags;

    /**
     * @var string
     */
    private $routingKey;

    public function __construct(string $body = '', array $properties = [], array $headers = [])
    {
        $this->body = $body;
        $this->properties = $properties;
        $this->headers = $headers;

        $this->redelivered = false;
        $this->flags = self::FLAG_NOPARAM;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperty(string $name, $value): void
    {
        if (null === $value) {
            unset($this->properties[$name]);
        } else {
            $this->properties[$name] = $value;
        }
    }

    public function getProperty(string $name, $default = null)
    {
        return array_key_exists($name, $this->properties) ? $this->properties[$name] : $default;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeader(string $name, $value): void
    {
        if (null === $value) {
            unset($this->headers[$name]);
        } else {
            $this->headers[$name] = $value;
        }
    }

    public function getHeader(string $name, $default = null)
    {
        return array_key_exists($name, $this->headers) ? $this->headers[$name] : $default;
    }

    public function setRedelivered(bool $redelivered): void
    {
        $this->redelivered = (bool) $redelivered;
    }

    public function isRedelivered(): bool
    {
        return $this->redelivered;
    }

    public function setCorrelationId(string $correlationId = null): void
    {
        $this->setHeader('correlation_id', $correlationId);
    }

    public function getCorrelationId(): ?string
    {
        return $this->getHeader('correlation_id');
    }

    public function setMessageId(string $messageId = null): void
    {
        $this->setHeader('message_id', $messageId);
    }

    public function getMessageId(): ?string
    {
        return $this->getHeader('message_id');
    }

    public function getTimestamp(): ?int
    {
        $value = $this->getHeader('timestamp');

        return $value === null ? null : (int) $value;
    }

    public function setTimestamp(int $timestamp = null): void
    {
        $this->setHeader('timestamp', $timestamp);
    }

    public function setReplyTo(string $replyTo = null): void
    {
        $this->setHeader('reply_to', $replyTo);
    }

    public function getReplyTo(): ?string
    {
        return $this->getHeader('reply_to');
    }

    public function setContentType(string $type = null): void
    {
        $this->setHeader('content_type', $type);
    }

    public function getContentType(): ?string
    {
        return $this->getHeader('content_type');
    }

    public function setContentEncoding(string $encoding = null): void
    {
        $this->setHeader('content_encoding', $encoding);
    }

    public function getContentEncoding(): ?string
    {
        return $this->getHeader('content_encoding');
    }

    public function getPriority(): ?int
    {
        return $this->getHeader('priority');
    }

    public function setPriority(int $priority = null): void
    {
        $this->setHeader('priority', $priority);
    }

    public function setDeliveryMode(int $deliveryMode = null): void
    {
        $this->setHeader('delivery_mode', $deliveryMode);
    }

    public function getDeliveryMode(): ?int
    {
        return $this->getHeader('delivery_mode');
    }

    public function setExpiration(int $expiration = null): void
    {
        // expiration is a string
        // https://www.rabbitmq.com/amqp-0-9-1-reference.html#domain.shortstr

        $this->setHeader('expiration', null === $expiration ? null : (string) $expiration);
    }

    public function getExpiration(): ?int
    {
        $expiration = $this->getHeader('expiration');

        return null === $expiration ? null : (int) $expiration;
    }

    public function getDeliveryTag(): ?int
    {
        return $this->deliveryTag;
    }

    public function setDeliveryTag(int $deliveryTag = null): void
    {
        $this->deliveryTag = $deliveryTag;
    }

    public function getConsumerTag(): ?string
    {
        return $this->consumerTag;
    }

    public function setConsumerTag(string $consumerTag = null): void
    {
        $this->consumerTag = $consumerTag;
    }

    public function clearFlags(): void
    {
        $this->flags = self::FLAG_NOPARAM;
    }

    public function addFlag(int $flag): void
    {
        $this->flags |= $flag;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function setFlags(int $flags): void
    {
        $this->flags = $flags;
    }

    public function getRoutingKey(): ?string
    {
        return $this->routingKey;
    }

    public function setRoutingKey(string $routingKey = null): void
    {
        $this->routingKey = $routingKey;
    }
}
