<?php

declare(strict_types=1);

namespace Intervention\Gif\Encoders;

use Intervention\Gif\Blocks\TableBasedImage;

class TableBasedImageEncoder extends AbstractEncoder
{
    /**
     * Create new instance
     *
     * @param TableBasedImage $source
     */
    public function __construct(TableBasedImage $source)
    {
        $this->source = $source;
    }

    public function encode(): string
    {
        return implode('', [
            $this->source->getImageDescriptor()->encode(),
            $this->source->getColorTable() ? $this->source->getColorTable()->encode() : '',
            $this->source->getImageData()->encode(),
        ]);
    }
}
