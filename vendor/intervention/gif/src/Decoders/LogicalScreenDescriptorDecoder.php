<?php

declare(strict_types=1);

namespace Intervention\Gif\Decoders;

use Intervention\Gif\Blocks\LogicalScreenDescriptor;

class LogicalScreenDescriptorDecoder extends AbstractPackedBitDecoder
{
    /**
     * Decode given string to current instance
     *
     * @return LogicalScreenDescriptor
     */
    public function decode(): LogicalScreenDescriptor
    {
        $logicalScreenDescriptor = new LogicalScreenDescriptor();

        // bytes 1-4
        $logicalScreenDescriptor->setSize(
            $this->decodeWidth($this->getNextBytes(2)),
            $this->decodeHeight($this->getNextBytes(2))
        );

        // byte 5
        $packedField = $this->getNextByte();

        $logicalScreenDescriptor->setGlobalColorTableExistance(
            $this->decodeGlobalColorTableExistance($packedField)
        );

        $logicalScreenDescriptor->setBitsPerPixel(
            $this->decodeBitsPerPixel($packedField)
        );

        $logicalScreenDescriptor->setGlobalColorTableSorted(
            $this->decodeGlobalColorTableSorted($packedField)
        );

        $logicalScreenDescriptor->setGlobalColorTableSize(
            $this->decodeGlobalColorTableSize($packedField)
        );

        // byte 6
        $logicalScreenDescriptor->setBackgroundColorIndex(
            $this->decodeBackgroundColorIndex($this->getNextByte())
        );

        // byte 7
        $logicalScreenDescriptor->setPixelAspectRatio(
            $this->decodePixelAspectRatio($this->getNextByte())
        );

        return $logicalScreenDescriptor;
    }

    /**
     * Decode width
     *
     * @return int
     */
    protected function decodeWidth(string $source): int
    {
        return unpack('v*', $source)[1];
    }

    /**
     * Decode height
     *
     * @return int
     */
    protected function decodeHeight(string $source): int
    {
        return unpack('v*', $source)[1];
    }

    /**
     * Decode existance of global color table
     *
     * @return bool
     */
    protected function decodeGlobalColorTableExistance(string $byte): bool
    {
        return $this->hasPackedBit($byte, 0);
    }

    /**
     * Decode color resolution in bits per pixel
     *
     * @return int
     */
    protected function decodeBitsPerPixel(string $byte): int
    {
        return bindec($this->getPackedBits($byte, 1, 3)) + 1;
    }

    /**
     * Decode global color table sorted status
     *
     * @return bool
     */
    protected function decodeGlobalColorTableSorted(string $byte): bool
    {
        return $this->hasPackedBit($byte, 4);
    }

    /**
     * Decode size of global color table
     *
     * @return int
     */
    protected function decodeGlobalColorTableSize(string $byte): int
    {
        return bindec($this->getPackedBits($byte, 5, 3));
    }

    /**
     * Decode background color index
     *
     * @return int
     */
    protected function decodeBackgroundColorIndex(string $source): int
    {
        return unpack('C', $source)[1];
    }

    /**
     * Decode pixel aspect ratio
     *
     * @return int
     */
    protected function decodePixelAspectRatio(string $source): int
    {
        return unpack('C', $source)[1];
    }
}
