<?php

namespace Interop\Amqp\Tests;

use Interop\Amqp\AmqpTopic as InteropAmqpTopic;
use Interop\Amqp\Impl\AmqpTopic;
use Interop\Queue\Topic;
use PHPUnit\Framework\TestCase;

class AmqpTopicTest extends TestCase
{
    public function testShouldImplementTopicInterface()
    {
        $this->assertInstanceOf(Topic::class, new AmqpTopic(''));
    }

    public function testShouldImplementAmqpTopicInterface()
    {
        $this->assertInstanceOf(InteropAmqpTopic::class, new AmqpTopic(''));
    }

    public function testShouldSetEmptyArrayAsArgumentsInConstructor()
    {
        $topic = new AmqpTopic('aName');

        $this->assertSame([], $topic->getArguments());
    }

    public function testShouldSetDirectTypeInConstructor()
    {
        $topic = new AmqpTopic('aName');

        $this->assertSame(AmqpTopic::TYPE_DIRECT, $topic->getType());
    }

    public function testShouldSetNoParamFlagInConstructor()
    {
        $topic = new AmqpTopic('aName');

        $this->assertSame(AmqpTopic::FLAG_NOPARAM, $topic->getFlags());
    }

    public function testShouldAllowAddFlags()
    {
        $topic = new AmqpTopic('aName');

        $topic->addFlag(AmqpTopic::FLAG_DURABLE);
        $topic->addFlag(AmqpTopic::FLAG_PASSIVE);

        $this->assertSame(AmqpTopic::FLAG_DURABLE | AmqpTopic::FLAG_PASSIVE, $topic->getFlags());
    }

    public function testShouldClearPreviouslySetFlags()
    {
        $topic = new AmqpTopic('aName');

        $topic->addFlag(AmqpTopic::FLAG_DURABLE);
        $topic->addFlag(AmqpTopic::FLAG_PASSIVE);

        //guard
        $this->assertSame(AmqpTopic::FLAG_DURABLE | AmqpTopic::FLAG_PASSIVE, $topic->getFlags());

        $topic->clearFlags();

        $this->assertSame(AmqpTopic::FLAG_NOPARAM, $topic->getFlags());
    }

    public function testShouldAllowGetPreviouslySetArguments()
    {
        $topic = new AmqpTopic('aName');

        $topic->setArguments(['foo' => 'fooVal', 'bar' => 'barVal']);

        $this->assertSame(['foo' => 'fooVal', 'bar' => 'barVal'], $topic->getArguments());
    }

    public function testShouldAllowGetPreviouslySetType()
    {
        $topic = new AmqpTopic('aName');

        $topic->setType(AmqpTopic::TYPE_FANOUT);

        $this->assertSame(AmqpTopic::TYPE_FANOUT, $topic->getType());
    }
}
