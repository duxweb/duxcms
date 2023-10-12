<?php

namespace App\Tools\Scheduler;

class Test
{
    public static function hello(): void
    {
        \Dux\App::log('debug')->info('test');
    }
}