<?php

declare(strict_types=1);

namespace Intervention\Gif\Blocks;

use Intervention\Gif\AbstractExtension;
use Intervention\Gif\DisposalMethod;

class GraphicControlExtension extends AbstractExtension
{
    public const LABEL = "\xF9";
    public const BLOCKSIZE = "\x04";

    /**
     * Existance flag of transparent color
     *
     * @var bool
     */
    protected bool $transparentColorExistance = false;

    /**
     * Transparent color index
     *
     * @var int
     */
    protected int $transparentColorIndex = 0;

    /**
     * User input flag
     *
     * @var bool
     */
    protected bool $userInput = false;

    /**
     * Create new instance
     *
     * @param int $delay
     * @param DisposalMethod $disposalMethod
     * @return void
     */
    public function __construct(
        protected int $delay = 0,
        protected DisposalMethod $disposalMethod = DisposalMethod::UNDEFINED,
    ) {
    }

    /**
     * Set delay time (1/100 second)
     *
     * @param int $value
     */
    public function setDelay(int $value): self
    {
        $this->delay = $value;

        return $this;
    }

    /**
     * Return delay time (1/100 second)
     *
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * Set disposal method
     *
     * @param DisposalMethod $method
     * @return self
     */
    public function setDisposalMethod(DisposalMethod $method): self
    {
        $this->disposalMethod = $method;

        return $this;
    }

    /**
     * Get disposal method
     *
     * @return DisposalMethod
     */
    public function getDisposalMethod(): DisposalMethod
    {
        return $this->disposalMethod;
    }

    /**
     * Get transparent color index
     *
     * @return int
     */
    public function getTransparentColorIndex(): int
    {
        return $this->transparentColorIndex;
    }

    /**
     * Set transparent color index
     *
     * @param int $index
     */
    public function setTransparentColorIndex(int $index): self
    {
        $this->transparentColorIndex = $index;

        return $this;
    }

    /**
     * Get current transparent color existance
     *
     * @return bool
     */
    public function getTransparentColorExistance(): bool
    {
        return $this->transparentColorExistance;
    }

    /**
     * Set existance flag of transparent color
     *
     * @param bool $existance
     */
    public function setTransparentColorExistance(bool $existance = true): self
    {
        $this->transparentColorExistance = $existance;

        return $this;
    }

    /**
     * Get user input flag
     *
     * @return bool
     */
    public function getUserInput(): bool
    {
        return $this->userInput;
    }

    /**
     * Set user input flag
     *
     * @param bool $value
     */
    public function setUserInput(bool $value = true): self
    {
        $this->userInput = $value;

        return $this;
    }
}
