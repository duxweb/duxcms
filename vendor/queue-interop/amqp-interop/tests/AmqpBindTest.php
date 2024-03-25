<?php

namespace Interop\Amqp\Tests;

use Interop\Amqp\AmqpBind as InteropAmqpBind;
use Interop\Amqp\Impl\AmqpBind;
use Interop\Amqp\Impl\AmqpTopic;
use PHPUnit\Framework\TestCase;

class AmqpBindTest extends TestCase
{
    public function testShouldImplementAmqpBindInterface()
    {
        $this->assertInstanceOf(InteropAmqpBind::class, new AmqpBind(new AmqpTopic(''), new AmqpTopic('')));
    }

    public function testShouldReturnTargetSetInConstructor()
    {
        $bind = new AmqpBind($target = new AmqpTopic(''), new AmqpTopic(''));

        $this->assertSame($target, $bind->getTarget());
    }

    public function testShouldReturnSourceSetInConstructor()
    {
        $bind = new AmqpBind(new AmqpTopic(''), $source = new AmqpTopic(''));

        $this->assertSame($source, $bind->getSource());
    }

    public function testShouldReturnRoutingKeySetInConstructor()
    {
        $bind = new AmqpBind(new AmqpTopic(''), new AmqpTopic(''), 'routing-key');

        $this->assertSame('routing-key', $bind->getRoutingKey());
    }

    public function testShouldReturnFlagsSetInConstructor()
    {
        $bind = new AmqpBind(new AmqpTopic(''), new AmqpTopic(''), null, 12345);

        $this->assertSame(12345, $bind->getFlags());
    }

    public function testShouldReturnArgumentsSetInConstructor()
    {
        $bind = new AmqpBind(new AmqpTopic(''), new AmqpTopic(''), null, 0, ['key' => 'value']);

        $this->assertSame(['key' => 'value'], $bind->getArguments());
    }
}
