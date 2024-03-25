<?php
declare(strict_types=1);

namespace Interop\Amqp;

use Interop\Queue\Topic;

interface AmqpTopic extends Topic, AmqpDestination
{
    const TYPE_DIRECT = 'direct';
    const TYPE_FANOUT = 'fanout';
    const TYPE_TOPIC = 'topic';
    const TYPE_HEADERS = 'headers';

    const FLAG_INTERNAL = 2048;

    public function getType(): string;

    public function setType(string $type): void;

    public function setFlags(int $flags): void;

    public function getFlags(): int;

    public function addFlag(int $flag): void;

    public function clearFlags(): void;

    public function getArguments(): array;

    public function setArguments(array $arguments);

    public function setArgument(string $key, $value): void;

    public function getArgument(string $key);
}
