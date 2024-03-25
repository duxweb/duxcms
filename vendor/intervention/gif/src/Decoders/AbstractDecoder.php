<?php

declare(strict_types=1);

namespace Intervention\Gif\Decoders;

abstract class AbstractDecoder
{
    /**
     * Decode current source
     *
     * @return mixed
     */
    abstract public function decode(): mixed;

    /**
     * Create new instance
     *
     * @param resource $handle
     * @param null|int $length
     */
    public function __construct(protected $handle, protected ?int $length = null)
    {
    }

    /**
     * Set source to decode
     *
     * @param resource $handle
     */
    public function setHandle($handle): self
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * Read given number of bytes and move file pointer
     *
     * @param int $length
     * @return string
     */
    protected function getNextBytes(int $length): string
    {
        return fread($this->handle, $length);
    }

    /**
     * Read given number of bytes and move pointer back to previous position
     *
     * @param int $length
     * @return string
     */
    protected function viewNextBytes(int $length): string
    {
        $bytes = $this->getNextBytes($length);
        $this->movePointer($length * -1);

        return $bytes;
    }

    /**
     * Read next byte and move pointer back to previous position
     *
     * @return string
     */
    protected function viewNextByte(): string
    {
        return $this->viewNextBytes(1);
    }

    /**
     * Read all remaining bytes from file handler
     *
     * @return string
     */
    protected function getRemainingBytes(): string
    {
        $all = '';
        do {
            $byte = fread($this->handle, 1);
            $all .= $byte;
        } while (!feof($this->handle));

        return $all;
    }

    /**
     * Get next byte in stream and move file pointer
     *
     * @return string
     */
    protected function getNextByte(): string
    {
        return $this->getNextBytes(1);
    }

    /**
     * Move file pointer on handle by given offset
     *
     * @param int $offset
     * @return self
     */
    protected function movePointer(int $offset): self
    {
        fseek($this->handle, $offset, SEEK_CUR);

        return $this;
    }

    /**
     * Decode multi byte value
     *
     * @return int
     */
    protected function decodeMultiByte(string $bytes): int
    {
        return unpack('v*', $bytes)[1];
    }

    /**
     * Set length
     *
     * @param int $length
     */
    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return null|int
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * Get current handle position
     *
     * @return int
     */
    public function getPosition(): int
    {
        return ftell($this->handle);
    }
}
