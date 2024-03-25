<?php

declare(strict_types=1);

namespace Intervention\Gif\Decoders;

use Intervention\Gif\Blocks\CommentExtension;

class CommentExtensionDecoder extends AbstractDecoder
{
    /**
     * Decode current source
     *
     * @return CommentExtension
     */
    public function decode(): CommentExtension
    {
        $this->getNextBytes(2); // skip marker & label

        $extension = new CommentExtension();
        foreach ($this->decodeComments() as $comment) {
            $extension->addComment($comment);
        }

        return $extension;
    }

    /**
     * Decode comment from current source
     *
     * @return array
     */
    protected function decodeComments(): array
    {
        $comments = [];

        do {
            $byte = $this->getNextByte();
            $size = $this->decodeBlocksize($byte);
            if ($size > 0) {
                $comments[] = $this->getNextBytes($size);
            }
        } while ($byte !== CommentExtension::TERMINATOR);

        return $comments;
    }

    /**
     * Decode blocksize of following comment
     *
     * @param string $byte
     * @return int
     */
    protected function decodeBlocksize(string $byte): int
    {
        return (int) @unpack('C', $byte)[1];
    }
}
