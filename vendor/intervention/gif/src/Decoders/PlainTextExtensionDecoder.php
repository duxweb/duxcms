<?php

declare(strict_types=1);

namespace Intervention\Gif\Decoders;

use Intervention\Gif\Blocks\PlainTextExtension;

class PlainTextExtensionDecoder extends AbstractDecoder
{
    /**
     * Decode current source
     *
     * @return PlainTextExtension
     */
    public function decode(): PlainTextExtension
    {
        $extension = new PlainTextExtension();

        // skip marker & label
        $this->getNextBytes(2);

        // skip info block
        $this->getNextBytes($this->getInfoBlockSize());

        // text blocks
        $extension->setText($this->decodeTextBlocks());

        return $extension;
    }

    /**
     * Get number of bytes in header block
     *
     * @return int
     */
    protected function getInfoBlockSize(): int
    {
        return unpack('C', $this->getNextByte())[1];
    }

    /**
     * Decode text sub blocks
     *
     * @return array
     */
    protected function decodeTextBlocks(): array
    {
        $blocks = [];

        do {
            $char = $this->getNextByte();
            $size = (int) unpack('C', $char)[1];
            if ($size > 0) {
                $blocks[] = $this->getNextBytes($size);
            }
        } while ($char !== PlainTextExtension::TERMINATOR);

        return $blocks;
    }
}
