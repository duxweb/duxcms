<?php

declare(strict_types=1);

namespace Intervention\Gif\Decoders;

use Intervention\Gif\Blocks\GraphicControlExtension;
use Intervention\Gif\DisposalMethod;

class GraphicControlExtensionDecoder extends AbstractPackedBitDecoder
{
    /**
     * Decode given string to current instance
     *
     * @return GraphicControlExtension
     */
    public function decode(): GraphicControlExtension
    {
        $result = new GraphicControlExtension();

        // bytes 1-3
        $this->getNextBytes(3); // skip marker, label & bytesize

        // byte #4
        $packedField = $this->getNextByte();
        $result->setDisposalMethod($this->decodeDisposalMethod($packedField));
        $result->setUserInput($this->decodeUserInput($packedField));
        $result->setTransparentColorExistance($this->decodeTransparentColorExistance($packedField));

        // bytes 5-6
        $result->setDelay($this->decodeDelay($this->getNextBytes(2)));

        // byte #7
        $result->setTransparentColorIndex($this->decodeTransparentColorIndex(
            $this->getNextByte()
        ));

        // byte #8 (terminator)
        $this->getNextByte();

        return $result;
    }

    /**
     * Decode disposal method
     *
     * @return DisposalMethod
     */
    protected function decodeDisposalMethod(string $byte): DisposalMethod
    {
        return DisposalMethod::from(
            bindec($this->getPackedBits($byte, 3, 3))
        );
    }

    /**
     * Decode user input flag
     *
     * @return bool
     */
    protected function decodeUserInput(string $byte): bool
    {
        return $this->hasPackedBit($byte, 6);
    }

    /**
     * Decode transparent color existance
     *
     * @return bool
     */
    protected function decodeTransparentColorExistance(string $byte): bool
    {
        return $this->hasPackedBit($byte, 7);
    }

    /**
     * Decode delay value
     *
     * @return int
     */
    protected function decodeDelay(string $bytes): int
    {
        return unpack('v*', $bytes)[1];
    }

    /**
     * Decode transparent color index
     *
     * @return int
     */
    protected function decodeTransparentColorIndex(string $byte): int
    {
        return unpack('C', $byte)[1];
    }
}
