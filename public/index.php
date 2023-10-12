<?php
$file = __DIR__ . '/../vendor/autoload.php';

if (!is_file($file)) {
    exit('Please run "composer intall" to install the dependencies, Composer is not installed, please install <a href="https://getcomposer.org/" target="_blank">Composer</a>.');
}

require $file;

// 运行框架
$app = \Dux\App::create(dirname(__DIR__));
$app->run();