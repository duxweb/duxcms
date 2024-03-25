<?php

namespace Interop\Amqp\Tests;

use Interop\Amqp\AmqpQueue as InteropAmqpQueue;
use Interop\Amqp\Impl\AmqpQueue;
use Interop\Queue\Queue;
use PHPUnit\Framework\TestCase;

class AmqpQueueTest extends TestCase
{
    public function testShouldImplementQueueInterface()
    {
        $this->assertInstanceOf(Queue::class, new AmqpQueue(''));
    }

    public function testShouldImplementInteropQueueInterface()
    {
        $this->assertInstanceOf(InteropAmqpQueue::class, new AmqpQueue(''));
    }

    public function testShouldSetEmptyArrayAsArgumentsInConstructor()
    {
        $queue = new AmqpQueue('aName');

        $this->assertSame([], $queue->getArguments());
    }

    public function testShouldSetNoParamFlagInConstructor()
    {
        $queue = new AmqpQueue('aName');

        $this->assertSame(AmqpQueue::FLAG_NOPARAM, $queue->getFlags());
    }

    public function testShouldAllowAddFlags()
    {
        $queue = new AmqpQueue('aName');

        $queue->addFlag(AmqpQueue::FLAG_DURABLE);
        $queue->addFlag(AmqpQueue::FLAG_PASSIVE);

        $this->assertSame(AmqpQueue::FLAG_DURABLE | AmqpQueue::FLAG_PASSIVE, $queue->getFlags());
    }

    public function testShouldClearPreviouslySetFlags()
    {
        $queue = new AmqpQueue('aName');

        $queue->addFlag(AmqpQueue::FLAG_DURABLE);
        $queue->addFlag(AmqpQueue::FLAG_PASSIVE);

        //guard
        $this->assertSame(AmqpQueue::FLAG_DURABLE | AmqpQueue::FLAG_PASSIVE, $queue->getFlags());

        $queue->clearFlags();

        $this->assertSame(AmqpQueue::FLAG_NOPARAM, $queue->getFlags());
    }

    public function testShouldAllowGetPreviouslySetArguments()
    {
        $queue = new AmqpQueue('aName');

        $queue->setArguments(['foo' => 'fooVal', 'bar' => 'barVal']);

        $this->assertSame(['foo' => 'fooVal', 'bar' => 'barVal'], $queue->getArguments());
    }

    public function testShouldReturnPreviouslySetConsumerTag()
    {
        $queue = new AmqpQueue('');

        $queue->setConsumerTag('theConsumerTag');

        $this->assertSame('theConsumerTag', $queue->getConsumerTag());
    }

}
