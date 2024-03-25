<?php

declare(strict_types=1);

namespace Intervention\Gif\Blocks;

use Intervention\Gif\AbstractExtension;

class PlainTextExtension extends AbstractExtension
{
    public const LABEL = "\x01";

    /**
     * Array of text
     *
     * @var array
     */
    protected array $text = [];

    /**
     * Get current text
     *
     * @return array
     */
    public function getText(): array
    {
        return $this->text;
    }

    /**
     * Add text
     *
     * @param string $text
     */
    public function addText(string $text): self
    {
        $this->text[] = $text;

        return $this;
    }

    /**
     * Set text array of extension
     *
     * @param array $text
     */
    public function setText(array $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Determine if any text is present
     *
     * @return bool
     */
    public function hasText(): bool
    {
        return count($this->text) > 0;
    }
}
