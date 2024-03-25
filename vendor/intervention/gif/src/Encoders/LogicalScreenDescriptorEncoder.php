<?php

declare(strict_types=1);

namespace Intervention\Gif\Encoders;

use Intervention\Gif\Blocks\LogicalScreenDescriptor;

class LogicalScreenDescriptorEncoder extends AbstractEncoder
{
    /**
     * Create new instance
     *
     * @param LogicalScreenDescriptor $source
     */
    public function __construct(LogicalScreenDescriptor $source)
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
            $this->encodeWidth(),
            $this->encodeHeight(),
            $this->encodePackedField(),
            $this->encodeBackgroundColorIndex(),
            $this->encodePixelAspectRatio(),
        ]);
    }

    /**
     * Encode width of current instance
     *
     * @return string
     */
    protected function encodeWidth(): string
    {
        return pack('v*', $this->source->getWidth());
    }

    /**
     * Encode height of current instance
     *
     * @return string
     */
    protected function encodeHeight(): string
    {
        return pack('v*', $this->source->getHeight());
    }

    /**
     * Encode background color index of global color table
     *
     * @return string
     */
    protected function encodeBackgroundColorIndex(): string
    {
        return pack('C', $this->source->getBackgroundColorIndex());
    }

    /**
     * Encode pixel aspect ratio
     *
     * @return string
     */
    protected function encodePixelAspectRatio(): string
    {
        return pack('C', $this->source->getPixelAspectRatio());
    }

    /**
     * Return color resolution for encoding
     *
     * @return string
     */
    protected function encodeColorResolution(): string
    {
        return str_pad(decbin($this->source->getBitsPerPixel() - 1), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Encode size of global color table
     *
     * @return string
     */
    protected function encodeGlobalColorTableSize(): string
    {
        return str_pad(decbin($this->source->getGlobalColorTableSize()), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Encode packed field of current instance
     *
     * @return string
     */
    protected function encodePackedField(): string
    {
        return pack('C', bindec(implode('', [
            (int) $this->source->getGlobalColorTableExistance(),
            $this->encodeColorResolution(),
            (int) $this->source->getGlobalColorTableSorted(),
            $this->encodeGlobalColorTableSize(),
        ])));
    }
}
