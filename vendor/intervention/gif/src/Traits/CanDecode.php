<?php

declare(strict_types=1);

namespace Intervention\Gif\Traits;

use Intervention\Gif\Decoders\AbstractDecoder;
use Intervention\Gif\Exceptions\DecoderException;

trait CanDecode
{
    /**
     * Decode current instance
     *
     * @param resource $source
     * @param null|int $length
     * @return mixed
     */
    public static function decode($source, ?int $length = null): mixed
    {
        return self::getDecoder($source, $length)->decode();
    }

    /**
     * Get decoder for current instance
     *
     * @param resource $source
     * @param null|int $length
     * @return AbstractDecoder
     */
    protected static function getDecoder($source, ?int $length = null): AbstractDecoder
    {
        $classname = self::getDecoderClassname();

        if (!class_exists($classname)) {
            throw new DecoderException("Decoder for '" . static::class . "' not found.");
        }

        return new $classname($source, $length);
    }

    /**
     * Get classname of decoder for current classname
     *
     * @return string
     */
    protected static function getDecoderClassname(): string
    {
        return sprintf('Intervention\Gif\Decoders\%sDecoder', self::getShortClassname());
    }
}
