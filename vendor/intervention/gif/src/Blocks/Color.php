<?php

declare(strict_types=1);

namespace Intervention\Gif\Blocks;

use Intervention\Gif\AbstractEntity;

class Color extends AbstractEntity
{
    /**
     * Create new instance
     *
     * @param int $r
     * @param int $g
     * @param int $b
     */
    public function __construct(
        protected int $r = 0,
        protected int $g = 0,
        protected int $b = 0
    ) {
    }

    /**
     * Get red value
     *
     * @return int
     */
    public function getRed()
    {
        return $this->r;
    }

    /**
     * Set red value
     *
     * @param int $value
     */
    public function setRed(int $value): self
    {
        $this->r = $value;

        return $this;
    }

    /**
     * Get green value
     *
     * @return int
     */
    public function getGreen()
    {
        return $this->g;
    }

    /**
     * Set green value
     *
     * @param int $value
     */
    public function setGreen(int $value): self
    {
        $this->g = $value;

        return $this;
    }

    /**
     * Get blue value
     *
     * @return int
     */
    public function getBlue()
    {
        return $this->b;
    }

    /**
     * Set blue value
     *
     * @param int $value
     */
    public function setBlue(int $value): self
    {
        $this->b = $value;

        return $this;
    }

    /**
     * Return hash value of current color
     *
     * @return string
     */
    public function getHash(): string
    {
        return md5($this->r . $this->g . $this->b);
    }
}
