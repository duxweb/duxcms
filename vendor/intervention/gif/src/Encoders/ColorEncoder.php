<?php

declare(strict_types=1);

namespace Intervention\Gif\Encoders;

use Intervention\Gif\Blocks\Color;

class ColorEncoder extends AbstractEncoder
{
    /**
     * Create new instance
     *
     * @param Color $source
     */
    public function __construct(Color $source)
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
            $this->encodeColorValue($this->source->getRed()),
            $this->encodeColorValue($this->source->getGreen()),
            $this->encodeColorValue($this->source->getBlue()),
        ]);
    }

    /**
     * Encode color value
     *
     * @return string
     */
    protected function encodeColorValue(int $value): string
    {
        return pack('C', $value);
    }
}
