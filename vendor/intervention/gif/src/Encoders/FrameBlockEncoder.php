<?php

declare(strict_types=1);

namespace Intervention\Gif\Encoders;

use Intervention\Gif\Blocks\FrameBlock;

class FrameBlockEncoder extends AbstractEncoder
{
    /**
     * Create new decoder instance
     *
     * @param FrameBlock $source
     */
    public function __construct(FrameBlock $source)
    {
        $this->source = $source;
    }

    public function encode(): string
    {
        $graphicControlExtension = $this->source->getGraphicControlExtension();
        $colorTable = $this->source->getColorTable();
        $plainTextExtension = $this->source->getPlainTextExtension();

        return implode('', [
            implode('', array_map(function ($extension) {
                return $extension->encode();
            }, $this->source->getApplicationExtensions())),
            implode('', array_map(function ($extension) {
                return $extension->encode();
            }, $this->source->getCommentExtensions())),
            $plainTextExtension ? $plainTextExtension->encode() : '',
            $graphicControlExtension ? $graphicControlExtension->encode() : '',
            $this->source->getImageDescriptor()->encode(),
            $colorTable ? $colorTable->encode() : '',
            $this->source->getImageData()->encode(),
        ]);
    }
}
