<?php
declare(strict_types=1);

namespace Interop\Amqp;

use Interop\Queue\Consumer;

/**
 * @method AmqpMessage|null receiveNoWait()
 * @method AmqpMessage|null receive(int $timeout = 0)
 * @method AmqpQueue getQueue()
 * @method void acknowledge(AmqpMessage $message)
 * @method void reject(AmqpMessage $message, bool $requeue)
 */
interface AmqpConsumer extends Consumer
{
    const FLAG_NOPARAM = 0;
    const FLAG_NOLOCAL = 1;
    const FLAG_NOACK = 2;
    const FLAG_EXCLUSIVE = 4;
    const FLAG_NOWAIT = 8;

    public function setConsumerTag(string $consumerTag = null): void;

    public function getConsumerTag(): ?string;

    public function clearFlags(): void;

    public function addFlag(int $flag): void;

    public function getFlags(): int;

    public function setFlags(int $flags): void;
}
