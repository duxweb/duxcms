#!/usr/bin/env php
<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$custom = dirname(__DIR__) . '/mime.types.custom';
if(file_exists($custom)) {
    $mime_types_custom_text = file_get_contents(dirname(__DIR__) . '/mime.types.custom');
} else {
    $mime_types_custom_text = "";
}

$mime_types_text = file_get_contents(dirname(__DIR__) . '/mime.types');

$generator = new \Mimey\MimeMappingGenerator($mime_types_custom_text . PHP_EOL . $mime_types_text);
$mapping_code = $generator->generateMappingCode();

file_put_contents(dirname(__DIR__) . '/mime.types.php', $mapping_code);
