<?php

declare(strict_types=1);

namespace Intervention\Gif\Encoders;

use Intervention\Gif\AbstractEntity;
use Intervention\Gif\Exceptions\EncoderException;
use Intervention\Gif\Blocks\ImageData;

class ImageDataEncoder extends AbstractEncoder
{
    /**
     * Create new instance
     *
     * @param ImageData $source
     */
    public function __construct(ImageData $source)
    {
        $this->source = $source;
    }

    /**
     * Encode current source
     *
     * @return string
     */
    public function encode(): string
    {
        if (!$this->source->hasBlocks()) {
            throw new EncoderException("No data blocks in ImageData.");
        }

        return implode('', [
            pack('C', $this->source->getLzwMinCodeSize()),
            implode('', array_map(function ($block) {
                return $block->encode();
            }, $this->source->getBlocks())),
            AbstractEntity::TERMINATOR,
        ]);
    }
}
