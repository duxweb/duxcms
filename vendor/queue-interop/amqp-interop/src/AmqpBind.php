<?php
declare(strict_types=1);

namespace Interop\Amqp;

interface AmqpBind
{
    const FLAG_NOPARAM = 0;
    const FLAG_NOWAIT = 1;

    /**
     * @return AmqpTopic|AmqpQueue
     */
    public function getTarget(): AmqpDestination;

    /**
     * @return AmqpTopic|AmqpQueue
     */
    public function getSource(): AmqpDestination;

    public function getRoutingKey(): ?string;

    public function getFlags(): int;

    public function getArguments(): array;
}
