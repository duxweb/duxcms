<?php

declare(strict_types=1);

namespace Intervention\Gif\Decoders;

use Intervention\Gif\Blocks\ImageDescriptor;

class ImageDescriptorDecoder extends AbstractPackedBitDecoder
{
    /**
     * Decode given string to current instance
     *
     * @return ImageDescriptor
     */
    public function decode(): ImageDescriptor
    {
        $descriptor = new ImageDescriptor();

        $this->getNextByte(); // skip separator

        $descriptor->setPosition(
            $this->decodeMultiByte($this->getNextBytes(2)),
            $this->decodeMultiByte($this->getNextBytes(2))
        );

        $descriptor->setSize(
            $this->decodeMultiByte($this->getNextBytes(2)),
            $this->decodeMultiByte($this->getNextBytes(2))
        );

        $packedField = $this->getNextByte();

        $descriptor->setLocalColorTableExistance(
            $this->decodeLocalColorTableExistance($packedField)
        );

        $descriptor->setLocalColorTableSorted(
            $this->decodeLocalColorTableSorted($packedField)
        );

        $descriptor->setLocalColorTableSize(
            $this->decodeLocalColorTableSize($packedField)
        );

        $descriptor->setInterlaced(
            $this->decodeInterlaced($packedField)
        );

        return $descriptor;
    }

    /**
     * Decode local color table existance
     *
     * @return bool
     */
    protected function decodeLocalColorTableExistance(string $byte): bool
    {
        return $this->hasPackedBit($byte, 0);
    }

    /**
     * Decode local color table sort method
     *
     * @return bool
     */
    protected function decodeLocalColorTableSorted(string $byte): bool
    {
        return $this->hasPackedBit($byte, 2);
    }

    /**
     * Decode local color table size
     *
     * @return int
     */
    protected function decodeLocalColorTableSize(string $byte): int
    {
        return bindec($this->getPackedBits($byte, 5, 3));
    }

    /**
     * Decode interlaced flag
     *
     * @return bool
     */
    protected function decodeInterlaced(string $byte): bool
    {
        return $this->hasPackedBit($byte, 1);
    }
}
