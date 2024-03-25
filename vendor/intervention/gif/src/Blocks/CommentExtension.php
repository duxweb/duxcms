<?php

declare(strict_types=1);

namespace Intervention\Gif\Blocks;

use Intervention\Gif\AbstractExtension;

class CommentExtension extends AbstractExtension
{
    public const LABEL = "\xFE";

    /**
     * Comment blocks
     *
     * @var array
     */
    protected array $comments = [];

    /**
     * Get all or one comment
     *
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Get one comment by key
     *
     * @param int $key
     * @return mixed
     */
    public function getComment(int $key): mixed
    {
        return array_key_exists($key, $this->comments) ? $this->comments[$key] : null;
    }

    /**
     * Set comment text
     *
     * @param string $value
     */
    public function addComment(string $value): self
    {
        $this->comments[] = $value;

        return $this;
    }
}
