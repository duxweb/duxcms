<?php

declare(strict_types=1);

namespace Intervention\Gif\Encoders;

use Intervention\Gif\Blocks\DataSubBlock;

class DataSubBlockEncoder extends AbstractEncoder
{
    /**
     * Create new instance
     *
     * @param DataSubBlock $source
     */
    public function __construct(DataSubBlock $source)
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
        return pack('C', $this->source->getSize()) . $this->source->getValue();
    }
}
