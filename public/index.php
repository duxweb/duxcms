<?php
require __DIR__ . '/../vendor/autoload.php';

// 运行框架
$app = \Dux\App::create(dirname(__DIR__));
$app->run();