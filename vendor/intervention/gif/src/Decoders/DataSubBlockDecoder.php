<?php

declare(strict_types=1);

namespace Intervention\Gif\Decoders;

use Intervention\Gif\Blocks\DataSubBlock;

class DataSubBlockDecoder extends AbstractDecoder
{
    /**
     * Decode current sourc
     *
     * @return DataSubBlock
     */
    public function decode(): DataSubBlock
    {
        $char = $this->getNextByte();
        $size = (int) unpack('C', $char)[1];

        return new DataSubBlock($this->getNextBytes($size));
    }
}
