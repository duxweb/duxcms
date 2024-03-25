<?php
declare(strict_types=1);

namespace Interop\Amqp;

use Interop\Queue\Queue;

interface AmqpQueue extends Queue, AmqpDestination
{
    const FLAG_EXCLUSIVE = 2097152;
    const FLAG_IFEMPTY = 4194304;

    public function setFlags(int $flags): void;

    public function getFlags(): int;

    public function addFlag(int $flag): void;

    public function clearFlags(): void;

    public function getArguments(): array;

    public function setArguments(array $arguments): void;

    public function setArgument(string $key, $value): void;

    public function getArgument(string $key);

    public function getConsumerTag(): ?string;

    public function setConsumerTag(string $consumerTag = null): void;
}
