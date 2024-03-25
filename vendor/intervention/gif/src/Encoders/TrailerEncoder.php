<?php

declare(strict_types=1);

namespace Intervention\Gif\Encoders;

use Intervention\Gif\Blocks\Trailer;

class TrailerEncoder extends AbstractEncoder
{
    /**
     * Create new instance
     *
     * @param Trailer $source
     */
    public function __construct(Trailer $source)
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
        return Trailer::MARKER;
    }
}
