<?php

declare(strict_types=1);

namespace Intervention\Gif;

use Intervention\Gif\Exceptions\DecoderException;
use Intervention\Gif\Traits\CanHandleFiles;

class Decoder
{
    use CanHandleFiles;

    /**
     * Decode given input
     *
     * @param mixed $input
     * @return GifDataStream
     */
    public static function decode(mixed $input): GifDataStream
    {
        return GifDataStream::decode(
            match (true) {
                self::isFilePath($input) => self::getHandleFromFilePath($input),
                is_string($input) => self::getHandleFromData($input),
                default => throw new DecoderException('Decoder input must be either file path or binary data.')
            }
        );
    }
}
