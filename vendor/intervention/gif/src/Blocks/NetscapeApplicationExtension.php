<?php

declare(strict_types=1);

namespace Intervention\Gif\Blocks;

class NetscapeApplicationExtension extends ApplicationExtension
{
    public const IDENTIFIER = "NETSCAPE";
    public const AUTH_CODE = "2.0";
    public const SUB_BLOCK_PREFIX = "\x01";

    public function __construct()
    {
        $this->setApplication(self::IDENTIFIER . self::AUTH_CODE);
        $this->setBlocks([new DataSubBlock(self::SUB_BLOCK_PREFIX . "\x00\x00")]);
    }

    public function getLoops(): int
    {
        return unpack('v*', substr($this->getBlocks()[0]->getValue(), 1))[1];
    }

    public function setLoops(int $loops): self
    {
        $this->setBlocks([
            new DataSubBlock(self::SUB_BLOCK_PREFIX . pack('v*', $loops))
        ]);

        return $this;
    }
}
