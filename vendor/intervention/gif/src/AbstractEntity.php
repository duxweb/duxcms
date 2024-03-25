<?php

declare(strict_types=1);

namespace Intervention\Gif;

use Intervention\Gif\Traits\CanDecode;
use Intervention\Gif\Traits\CanEncode;
use ReflectionClass;

abstract class AbstractEntity
{
    use CanEncode;
    use CanDecode;

    public const TERMINATOR = "\x00";

    /**
     * Get short classname of current instance
     *
     * @return string
     */
    public static function getShortClassname(): string
    {
        return (new ReflectionClass(static::class))->getShortName();
    }

    /**
     * Cast object to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->encode();
    }
}
