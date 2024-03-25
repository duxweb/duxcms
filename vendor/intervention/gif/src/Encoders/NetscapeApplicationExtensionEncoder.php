<?php

declare(strict_types=1);

namespace Intervention\Gif\Encoders;

use Intervention\Gif\Blocks\ApplicationExtension;
use Intervention\Gif\Blocks\NetscapeApplicationExtension;

class NetscapeApplicationExtensionEncoder extends ApplicationExtensionEncoder
{
    /**
     * Create new decoder instance
     *
     * @param NetscapeApplicationExtension $source
     */
    public function __construct(NetscapeApplicationExtension $source)
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
        return implode('', [
            ApplicationExtension::MARKER,
            ApplicationExtension::LABEL,
            pack('C', $this->source->getBlockSize()),
            $this->source->getApplication(),
            implode('', array_map(function ($block) {
                return $block->encode();
            }, $this->source->getBlocks())),
            ApplicationExtension::TERMINATOR,
        ]);
    }
}
