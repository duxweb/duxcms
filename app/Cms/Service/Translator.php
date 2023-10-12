<?php

namespace App\Cms\Service;

class Translator
{
    public function __construct()
    {
    }

    public function translate(string $original, ...$params): string
    {
        return __($original, 'theme', $params);
    }
}