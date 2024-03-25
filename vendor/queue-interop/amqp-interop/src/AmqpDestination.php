<?php
declare(strict_types=1);

namespace Interop\Amqp;

interface AmqpDestination
{
    const FLAG_NOPARAM = 0;
    const FLAG_PASSIVE = 1;
    const FLAG_DURABLE = 2;
    const FLAG_AUTODELETE = 4;
    const FLAG_NOWAIT = 8;
    const FLAG_IFUNUSED = 16;
}
