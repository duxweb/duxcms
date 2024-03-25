<?php

declare(strict_types=1);

namespace Intervention\Gif\Decoders;

use Intervention\Gif\AbstractEntity;
use Intervention\Gif\Blocks\DataSubBlock;
use Intervention\Gif\Blocks\ImageData;

class ImageDataDecoder extends AbstractDecoder
{
    /**
     * Decode current source
     *
     * @return ImageData
     */
    public function decode(): ImageData
    {
        $data = new ImageData();

        // LZW min. code size
        $char = $this->getNextByte();
        $size = (int) unpack('C', $char)[1];
        $data->setLzwMinCodeSize($size);

        do {
            // decode sub blocks
            $char = $this->getNextByte();
            $size = (int) unpack('C', $char)[1];
            if ($size > 0) {
                $data->addBlock(new DataSubBlock($this->getNextBytes($size)));
            }
        } while ($char !== AbstractEntity::TERMINATOR);

        return $data;
    }
}
