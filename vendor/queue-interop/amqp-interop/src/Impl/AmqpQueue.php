<?php
declare(strict_types=1);

namespace Interop\Amqp\Impl;

use Interop\Amqp\AmqpQueue as InteropAmqpQueue;

final class AmqpQueue implements InteropAmqpQueue
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $flags;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var string
     */
    private $consumerTag;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->arguments = [];
        $this->flags = self::FLAG_NOPARAM;
    }

    public function getQueueName(): string
    {
        return $this->name;
    }

    public function getConsumerTag(): ?string
    {
        return $this->consumerTag;
    }

    public function setConsumerTag(string $consumerTag = null): void
    {
        $this->consumerTag = $consumerTag;
    }

    public function addFlag(int $flag): void
    {
        $this->flags |= $flag;
    }

    public function clearFlags(): void
    {
        $this->flags = self::FLAG_NOPARAM;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function setFlags(int $flags): void
    {
        $this->flags = $flags;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    public function setArgument(string $key, $value): void
    {
        $this->arguments[$key] = $value;
    }

    public function getArgument(string $key, $default = null)
    {
        return array_key_exists($key, $this->arguments) ? $this->arguments[$key] : $default;
    }
}
