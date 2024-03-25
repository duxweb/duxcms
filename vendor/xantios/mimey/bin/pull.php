#!/usr/bin/env php
<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$source = 'https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';
$content = file_get_contents($source);

file_put_contents(getcwd().DIRECTORY_SEPARATOR."mime.types",$content);

print "Pulled new mime.types file. Run generate now? [y/N]";
$input = fopen("php://stdin","r");

if(strtolower(trim(fgets($input)))=="y") {
    exec(getcwd().DIRECTORY_SEPARATOR.'bin/generate.php');
}

fclose($input);