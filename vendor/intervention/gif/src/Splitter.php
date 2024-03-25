<?php

declare(strict_types=1);

namespace Intervention\Gif;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Splitter implements IteratorAggregate
{
    /**
     * Single frames
     *
     * @var array
     */
    protected array $frames = [];

    /**
     * Delays of each frame
     *
     * @var array
     */
    protected array $delays = [];

    /**
     * Create new instance
     *
     * @param GifDataStream $stream
     */
    public function __construct(protected GifDataStream $stream)
    {
    }

    /**
     * Iterator
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->frames);
    }

    /**
     * Get frames
     *
     * @return array
     */
    public function getFrames(): array
    {
        return $this->frames;
    }

    /**
     * Get delays
     *
     * @return array
     */
    public function getDelays(): array
    {
        return $this->delays;
    }

    /**
     * Set stream of instance
     *
     * @param GifDataStream $stream
     */
    public function setStream(GifDataStream $stream): self
    {
        $this->stream = $stream;

        return $this;
    }

    /**
     * Static constructor method
     *
     * @param GifDataStream $stream
     * @return Splitter
     */
    public static function create(GifDataStream $stream): self
    {
        return new self($stream);
    }

    /**
     * Split current stream into array of seperate streams for each frame
     *
     * @return Splitter
     */
    public function split(): self
    {
        $this->frames = [];

        foreach ($this->stream->getFrames() as $frame) {
            // create separate stream for each frame
            $gif = Builder::canvas(
                $this->stream->getLogicalScreenDescriptor()->getWidth(),
                $this->stream->getLogicalScreenDescriptor()->getHeight()
            )->getGifDataStream();

            // check if working stream has global color table
            if ($this->stream->hasGlobalColorTable()) {
                $gif->setGlobalColorTable($this->stream->getGlobalColorTable());
                $gif->getLogicalScreenDescriptor()->setGlobalColorTableExistance(true);

                $gif->getLogicalScreenDescriptor()->setGlobalColorTableSorted(
                    $this->stream->getLogicalScreenDescriptor()->getGlobalColorTableSorted()
                );

                $gif->getLogicalScreenDescriptor()->setGlobalColorTableSize(
                    $this->stream->getLogicalScreenDescriptor()->getGlobalColorTableSize()
                );

                $gif->getLogicalScreenDescriptor()->setBackgroundColorIndex(
                    $this->stream->getLogicalScreenDescriptor()->getBackgroundColorIndex()
                );

                $gif->getLogicalScreenDescriptor()->setPixelAspectRatio(
                    $this->stream->getLogicalScreenDescriptor()->getPixelAspectRatio()
                );

                $gif->getLogicalScreenDescriptor()->setBitsPerPixel(
                    $this->stream->getLogicalScreenDescriptor()->getBitsPerPixel()
                );
            }

            // copy original frame
            $gif->addFrame($frame);

            $this->frames[] = $gif;
            $this->delays[] = match (is_object($frame->getGraphicControlExtension())) {
                true => $frame->getGraphicControlExtension()->getDelay(),
                default => 0,
            };
        }

        return $this;
    }

    /**
     * Return array of GD library resources for each frame
     *
     * @return array
     */
    public function toResources(): array
    {
        $resources = [];

        foreach ($this->frames as $frame) {
            if (is_a($frame, GifDataStream::class)) {
                $resource = imagecreatefromstring($frame->encode());
                imagepalettetotruecolor($resource);
                imagesavealpha($resource, true);
                $resources[] = $resource;
            }
        }

        return $resources;
    }

    /**
     * Return array of coalesced GD library resources for each frame
     *
     * @return array
     */
    public function coalesceToResources(): array
    {
        $resources = $this->toResources();

        // static gif files don't need to be coalesced
        if (count($resources) === 1) {
            return $resources;
        }

        $width = imagesx($resources[0]);
        $height = imagesy($resources[0]);
        $transparent = imagecolortransparent($resources[0]);

        foreach ($resources as $key => $resource) {
            // get meta data
            $gif = $this->frames[$key];
            $descriptor = $gif->getFirstFrame()->getImageDescriptor();
            $offset_x = $descriptor->getLeft();
            $offset_y = $descriptor->getTop();
            $w = $descriptor->getWidth();
            $h = $descriptor->getHeight();

            if (in_array($this->getDisposalMethod($gif), [DisposalMethod::NONE, DisposalMethod::PREVIOUS])) {
                if ($key >= 1) {
                    // create normalized gd image
                    $canvas = imagecreatetruecolor($width, $height);
                    if (imagecolortransparent($resource) != -1) {
                        $transparent = imagecolortransparent($resource);
                    } else {
                        $transparent = imagecolorallocatealpha($resource, 255, 0, 255, 127);
                    }

                    // fill with transparent
                    imagefill($canvas, 0, 0, $transparent);
                    imagecolortransparent($canvas, $transparent);
                    imagealphablending($canvas, true);

                    // insert last as base
                    imagecopy(
                        $canvas,
                        $resources[$key - 1],
                        0,
                        0,
                        0,
                        0,
                        $width,
                        $height
                    );

                    // insert resource
                    imagecopy(
                        $canvas,
                        $resource,
                        $offset_x,
                        $offset_y,
                        0,
                        0,
                        $w,
                        $h
                    );
                } else {
                    imagealphablending($resource, true);
                    $canvas = $resource;
                }
            } else {
                // create normalized gd image
                $canvas = imagecreatetruecolor($width, $height);
                if (imagecolortransparent($resource) != -1) {
                    $transparent = imagecolortransparent($resource);
                } else {
                    $transparent = imagecolorallocatealpha($resource, 255, 0, 255, 127);
                }

                // fill with transparent
                imagefill($canvas, 0, 0, $transparent);
                imagecolortransparent($canvas, $transparent);
                imagealphablending($canvas, true);

                // insert frame resource
                imagecopy(
                    $canvas,
                    $resource,
                    $offset_x,
                    $offset_y,
                    0,
                    0,
                    $w,
                    $h
                );
            }

            $resources[$key] = $canvas;
        }

        return $resources;
    }

    /**
     * Find and return disposal method of given gif data stream
     *
     * @param GifDataStream $gif
     * @return DisposalMethod
     */
    private function getDisposalMethod(GifDataStream $gif): DisposalMethod
    {
        return $gif->getFirstFrame()->getGraphicControlExtension()->getDisposalMethod();
    }
}
