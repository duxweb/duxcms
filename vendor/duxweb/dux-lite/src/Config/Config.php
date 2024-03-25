<?php
declare(strict_types=1);

namespace Dux\Config;

class Config
{
    static array $variables = [];

    static array $tags = [];


    public static function setValues(array $data): void
    {
        self::$variables = [...self::$variables, ...$data];
    }

    public static function setValue(string $key, mixed $value): void
    {
        self::$variables[$key] = $value;
    }

    public static function getValue(string $key): mixed
    {
        return self::$variables[$key];
    }

    public static function setTags(array $data): void
    {
        self::$tags = [...self::$tags, ...$data];
    }

    public static function setTag(string $key, callable $fun): void
    {
        self::$tags[$key] = $fun;
    }

    public static function getTag(string $key): mixed
    {
        return self::$tags[$key];
    }
}