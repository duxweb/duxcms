<?php

declare(strict_types=1);

namespace Intervention\Gif\Blocks;

use Intervention\Gif\AbstractExtension;

class ApplicationExtension extends AbstractExtension
{
    public const LABEL = "\xFF";

    /**
     * Application Identifier & Auth Code
     *
     * @var string
     */
    protected string $application = '';

    /**
     * Data Sub Blocks
     *
     * @var array
     */
    protected array $blocks = [];

    public function getBlockSize(): int
    {
        return strlen($this->application);
    }

    public function setApplication(string $value): self
    {
        $this->application = $value;

        return $this;
    }

    public function getApplication(): string
    {
        return $this->application;
    }

    public function addBlock(DataSubBlock $block): self
    {
        $this->blocks[] = $block;

        return $this;
    }

    public function setBlocks(array $blocks): self
    {
        $this->blocks = $blocks;

        return $this;
    }

    public function getBlocks(): array
    {
        return $this->blocks;
    }
}
