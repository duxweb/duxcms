<?php

namespace Interop\Amqp\Tests;

use Interop\Amqp\AmqpMessage as InteropAmqpMessage;
use Interop\Amqp\Impl\AmqpMessage;
use Interop\Queue\Message;
use PHPUnit\Framework\TestCase;

class AmqpMessageTest extends TestCase
{
    public function testShouldImplementMessageInterface()
    {
        $this->assertInstanceOf(Message::class, new AmqpMessage());
    }

    public function testShouldImplementAmqpQueueInterface()
    {
        $this->assertInstanceOf(InteropAmqpMessage::class, new AmqpMessage());
    }

    public function testShouldSetNoParamFlagInConstructor()
    {
        $message = new AmqpMessage();

        $this->assertSame(AmqpMessage::FLAG_NOPARAM, $message->getFlags());
    }

    public function testShouldReturnPreviouslySetContentType()
    {
        $message = new AmqpMessage();

        $message->setContentType('theContentType');

        $this->assertSame('theContentType', $message->getContentType());
        $this->assertSame(['content_type' => 'theContentType'], $message->getHeaders());
    }

    public function testShouldReturnPreviouslySetContentEncoding()
    {
        $message = new AmqpMessage();

        $message->setContentEncoding('theContentEncoding');

        $this->assertSame('theContentEncoding', $message->getContentEncoding());
        $this->assertSame(['content_encoding' => 'theContentEncoding'], $message->getHeaders());
    }

    public function testShouldReturnPreviouslySetDeliveryMode()
    {
        $message = new AmqpMessage();

        $message->setDeliveryMode(149);

        $this->assertSame(149, $message->getDeliveryMode());
        $this->assertSame(['delivery_mode' => 149], $message->getHeaders());
    }

    public function testShouldReturnPreviouslySetExpiration()
    {
        $message = new AmqpMessage();

        $message->setExpiration(123490);

        $this->assertSame(123490, $message->getExpiration());
        $this->assertSame(['expiration' => '123490'], $message->getHeaders());
    }

    public function testShouldReturnPreviouslySetPriority()
    {
        $message = new AmqpMessage();

        $message->setPriority(3);

        $this->assertSame(3, $message->getPriority());
        $this->assertSame(['priority' => 3], $message->getHeaders());
    }

    public function testShouldReturnPreviouslySetDeliveryTag()
    {
        $message = new AmqpMessage();

        $message->setDeliveryTag(123);

        $this->assertSame(123, $message->getDeliveryTag());
    }

    public function testShouldReturnPreviouslySetConsumerTag()
    {
        $message = new AmqpMessage();

        $message->setConsumerTag('theConsumerTag');

        $this->assertSame('theConsumerTag', $message->getConsumerTag());
    }

    public function testShouldAllowSetFlags()
    {
        $message = new AmqpMessage();

        $message->setFlags(12345);

        $this->assertSame(12345, $message->getFlags());
    }

    public function testShouldAllowAddFlags()
    {
        $message = new AmqpMessage();

        $message->addFlag(AmqpMessage::FLAG_MANDATORY);
        $message->addFlag(AmqpMessage::FLAG_IMMEDIATE);

        $this->assertSame(AmqpMessage::FLAG_IMMEDIATE | AmqpMessage::FLAG_MANDATORY, $message->getFlags());
    }

    public function testShouldClearPreviouslySetFlags()
    {
        $message = new AmqpMessage();

        $message->addFlag(AmqpMessage::FLAG_MANDATORY);
        $message->addFlag(AmqpMessage::FLAG_IMMEDIATE);

        //guard
        $this->assertSame(AmqpMessage::FLAG_IMMEDIATE | AmqpMessage::FLAG_MANDATORY, $message->getFlags());

        $message->clearFlags();

        $this->assertSame(AmqpMessage::FLAG_NOPARAM, $message->getFlags());
    }

    public function testShouldReturnPreviouslySetRoutingKey()
    {
        $message = new AmqpMessage();

        $message->setRoutingKey('theRoutingKey');

        $this->assertSame('theRoutingKey', $message->getRoutingKey());
    }

    public function testShouldUnsetHeaderIfNullPassed()
    {
        $message = new AmqpMessage();

        $message->setHeader('aHeader', 'aVal');

        //guard
        $this->assertSame('aVal', $message->getHeader('aHeader'));

        $message->setHeader('aHeader', null);

        $this->assertNull($message->getHeader('aHeader'));
        $this->assertSame([], $message->getHeaders());
    }

    public function testShouldUnsetPropertyIfNullPassed()
    {
        $message = new AmqpMessage();

        $message->setProperty('aProperty', 'aVal');

        //guard
        $this->assertSame('aVal', $message->getProperty('aProperty'));

        $message->setProperty('aProperty', null);

        $this->assertNull($message->getProperty('aProperty'));
        $this->assertSame([], $message->getProperties());
    }
}
