<?php

namespace Dux\Queue;

use Dux\App;
use Exception;
use Interop\Queue\Processor;
use Interop\Queue\Message;
use Interop\Queue\Context;

class QueueProcessor implements Processor
{
    public function process(Message $message, Context $context): object|string
    {
        $data = json_decode($message->getBody(), true);
        if (!$data) {
            return self::REJECT;
        }

        if (!class_exists($data['class'])) {
            return self::REJECT;
        }
        if (!method_exists($data['class'], $data['method'])) {
            return self::REJECT;
        }

        try {
            $object = new $data['class'];
            call_user_func([$object, $data['method']], $data['params']);
        } catch (Exception $e) {
            App::log('queue')->error($e->getMessage(), [
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            return self::REQUEUE;
        }
        return self::ACK;
    }
}