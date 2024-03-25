<?php

declare(strict_types=1);

namespace Intervention\Gif\Blocks;

use Intervention\Gif\AbstractEntity;

class LogicalScreenDescriptor extends AbstractEntity
{
    /**
     * Width
     *
     * @var int
     */
    protected int $width;

    /**
     * Height
     *
     * @var int
     */
    protected int $height;

    /**
     * Global color table flag
     *
     * @var bool
     */
    protected bool $globalColorTableExistance = false;

    /**
     * Sort flag of global color table
     *
     * @var bool
     */
    protected bool $globalColorTableSorted = false;

    /**
     * Size of global color table
     *
     * @var int
     */
    protected int $globalColorTableSize = 0;

    /**
     * Background color index
     *
     * @var int
     */
    protected int $backgroundColorIndex = 0;

    /**
     * Color resolution
     *
     * @var int
     */
    protected int $bitsPerPixel = 8;

    /**
     * Pixel aspect ration
     *
     * @var int
     */
    protected int $pixelAspectRatio = 0;

    /**
     * Set size
     *
     * @param int $width
     * @param int $height
     */
    public function setSize(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    /**
     * Get width of current instance
     *
     * @return int
     */
    public function getWidth(): int
    {
        return intval($this->width);
    }

    /**
     * Get height of current instance
     *
     * @return int
     */
    public function getHeight(): int
    {
        return intval($this->height);
    }

    /**
     * Determine if global color table is present
     *
     * @return bool
     */
    public function getGlobalColorTableExistance(): bool
    {
        return $this->globalColorTableExistance;
    }

    /**
     * Alias of getGlobalColorTableExistance
     *
     * @return bool
     */
    public function hasGlobalColorTable(): bool
    {
        return $this->getGlobalColorTableExistance();
    }

    /**
     * Set global color table flag
     *
     * @param bool $existance
     * @return self
     */
    public function setGlobalColorTableExistance($existance = true): self
    {
        $this->globalColorTableExistance = $existance;

        return $this;
    }

    /**
     * Get global color table sorted flag
     *
     * @return bool
     */
    public function getGlobalColorTableSorted(): bool
    {
        return $this->globalColorTableSorted;
    }

    /**
     * Set global color table sorted flag
     *
     * @param bool $sorted
     * @return self
     */
    public function setGlobalColorTableSorted($sorted = true): self
    {
        $this->globalColorTableSorted = $sorted;

        return $this;
    }

    /**
     * Get size of global color table
     *
     * @return int
     */
    public function getGlobalColorTableSize(): int
    {
        return $this->globalColorTableSize;
    }

    /**
     * Get byte size of global color table
     *
     * @return int
     */
    public function getGlobalColorTableByteSize(): int
    {
        return 3 * pow(2, $this->getGlobalColorTableSize() + 1);
    }

    /**
     * Set size of global color table
     *
     * @param int $size
     */
    public function setGlobalColorTableSize(int $size): self
    {
        $this->globalColorTableSize = $size;

        return $this;
    }

    /**
     * Get background color index
     *
     * @return int
     */
    public function getBackgroundColorIndex(): int
    {
        return $this->backgroundColorIndex;
    }

    /**
     * Set background color index
     *
     * @param int $index
     */
    public function setBackgroundColorIndex(int $index): self
    {
        $this->backgroundColorIndex = $index;

        return $this;
    }

    /**
     * Get current pixel aspect ration
     *
     * @return int
     */
    public function getPixelAspectRatio(): int
    {
        return $this->pixelAspectRatio;
    }

    /**
     * Set pixel aspect ratio
     *
     * @param int $ratio
     */
    public function setPixelAspectRatio(int $ratio): self
    {
        $this->pixelAspectRatio = $ratio;

        return $this;
    }

    /**
     * Get color resolution
     *
     * @return int
     */
    public function getBitsPerPixel()
    {
        return $this->bitsPerPixel;
    }

    /**
     * Set color resolution
     *
     * @param int $value
     */
    public function setBitsPerPixel(int $value): self
    {
        $this->bitsPerPixel = $value;

        return $this;
    }
}
