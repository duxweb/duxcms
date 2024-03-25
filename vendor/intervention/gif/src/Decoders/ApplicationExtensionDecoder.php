<?php

declare(strict_types=1);

namespace Intervention\Gif\Decoders;

use Intervention\Gif\Blocks\ApplicationExtension;
use Intervention\Gif\Blocks\DataSubBlock;
use Intervention\Gif\Blocks\NetscapeApplicationExtension;

class ApplicationExtensionDecoder extends AbstractDecoder
{
    /**
     * Decode current source
     *
     * @return ApplicationExtension
     */
    public function decode(): ApplicationExtension
    {
        $result = new ApplicationExtension();

        $this->getNextByte(); // marker
        $this->getNextByte(); // label
        $blocksize = $this->decodeBlockSize($this->getNextByte());
        $application = $this->getNextBytes($blocksize);

        if ($application === NetscapeApplicationExtension::IDENTIFIER . NetscapeApplicationExtension::AUTH_CODE) {
            $result = new NetscapeApplicationExtension();

            // skip length
            $this->getNextByte();

            $result->setBlocks([
                new DataSubBlock(
                    $this->getNextBytes(3)
                )
            ]);

            // skip terminator
            $this->getNextByte();

            return $result;
        }

        $result->setApplication($application);

        // decode data sub blocks
        $blocksize = $this->decodeBlockSize($this->getNextByte());
        while ($blocksize > 0) {
            $result->addBlock(new DataSubBlock($this->getNextBytes($blocksize)));
            $blocksize = $this->decodeBlockSize($this->getNextByte());
        }

        return $result;
    }

    /**
     * Decode block size of ApplicationExtension from given byte
     *
     * @param string $byte
     * @return int
     */
    protected function decodeBlockSize(string $byte): int
    {
        return (int) @unpack('C', $byte)[1];
    }
}
