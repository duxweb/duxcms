<?php

declare(strict_types=1);

namespace Intervention\Gif\Blocks;

use Intervention\Gif\AbstractEntity;

class ImageDescriptor extends AbstractEntity
{
    public const SEPARATOR = "\x2C";

    /**
     * Width of frame
     *
     * @var int
     */
    protected int $width = 0;

    /**
     * Height of frame
     *
     * @var int
     */
    protected int $height = 0;

    /**
     * Left position of frame
     *
     * @var int
     */
    protected int $left = 0;

    /**
     * Top position of frame
     *
     * @var int
     */
    protected int $top = 0;

    /**
     * Determine if frame is interlaced
     *
     * @var bool
     */
    protected bool $interlaced = false;

    /**
     * Local color table flag
     *
     * @var bool
     */
    protected bool $localColorTableExistance = false;

    /**
     * Sort flag of local color table
     *
     * @var bool
     */
    protected bool $localColorTableSorted = false;

    /**
     * Size of local color table
     *
     * @var int
     */
    protected int $localColorTableSize = 0;

    /**
     * Get current width
     *
     * @return int
     */
    public function getWidth(): int
    {
        return intval($this->width);
    }

    /**
     * Get current width
     *
     * @return int
     */
    public function getHeight(): int
    {
        return intval($this->height);
    }

    /**
     * Get current Top
     *
     * @return int
     */
    public function getTop(): int
    {
        return intval($this->top);
    }

    /**
     * Get current Left
     *
     * @return int
     */
    public function getLeft(): int
    {
        return intval($this->left);
    }

    /**
     * Set size of current instance
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
     * Set position of current instance
     *
     * @param int $left
     * @param int $top
     */
    public function setPosition(int $left, int $top): self
    {
        $this->left = $left;
        $this->top = $top;

        return $this;
    }

    /**
     * Determine if frame is interlaced
     *
     * @return bool
     */
    public function isInterlaced(): bool
    {
        return $this->interlaced === true;
    }

    /**
     * Set or unset interlaced value
     *
     * @param bool $value
     */
    public function setInterlaced(bool $value = true): self
    {
        $this->interlaced = $value;

        return $this;
    }

    /**
     * Determine if local color table is present
     *
     * @return bool
     */
    public function getLocalColorTableExistance(): bool
    {
        return $this->localColorTableExistance;
    }

    /**
     * Alias for getLocalColorTableExistance
     *
     * @return bool
     */
    public function hasLocalColorTable(): bool
    {
        return $this->getLocalColorTableExistance();
    }

    /**
     * Set local color table flag
     *
     * @param bool $existance
     * @return self
     */
    public function setLocalColorTableExistance($existance = true): self
    {
        $this->localColorTableExistance = $existance;

        return $this;
    }

    /**
     * Get local color table sorted flag
     *
     * @return bool
     */
    public function getLocalColorTableSorted(): bool
    {
        return $this->localColorTableSorted;
    }

    /**
     * Set local color table sorted flag
     *
     * @param bool $sorted
     * @return self
     */
    public function setLocalColorTableSorted($sorted = true): self
    {
        $this->localColorTableSorted = $sorted;

        return $this;
    }

    /**
     * Get size of local color table
     *
     * @return int
     */
    public function getLocalColorTableSize(): int
    {
        return $this->localColorTableSize;
    }

    /**
     * Get byte size of global color table
     *
     * @return int
     */
    public function getLocalColorTableByteSize(): int
    {
        return 3 * pow(2, $this->getLocalColorTableSize() + 1);
    }

    /**
     * Set size of local color table
     *
     * @param int $size
     */
    public function setLocalColorTableSize(int $size): self
    {
        $this->localColorTableSize = $size;

        return $this;
    }
}
