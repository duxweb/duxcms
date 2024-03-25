<?php

declare(strict_types=1);

namespace Intervention\Gif;

use Exception;
use Intervention\Gif\Blocks\FrameBlock;
use Intervention\Gif\Blocks\GraphicControlExtension;
use Intervention\Gif\Blocks\ImageDescriptor;
use Intervention\Gif\Blocks\NetscapeApplicationExtension;
use Intervention\Gif\Blocks\TableBasedImage;
use Intervention\Gif\Traits\CanHandleFiles;

class Builder
{
    use CanHandleFiles;

    /**
     * Create new instance
     *
     * @param GifDataStream $gif
     * @return void
     */
    public function __construct(protected GifDataStream $gif = new GifDataStream())
    {
    }

    /**
     * Get GifDataStream object we're currently building
     *
     * @return GifDataStream
     */
    public function getGifDataStream(): GifDataStream
    {
        return $this->gif;
    }

    /**
     * Set canvas size of gif
     *
     * @param int $width
     * @param int $height
     * @return Builder
     */
    public function setSize(int $width, int $height): self
    {
        $this->gif->getLogicalScreenDescriptor()->setSize($width, $height);

        return $this;
    }

    /**
     * Create new canvas
     *
     * @param int $width
     * @param int $height
     * @return self
     */
    public static function canvas(int $width, int $height): self
    {
        return (new self())->setSize($width, $height);
    }

    /**
     * Set loop count
     *
     * @param int $loops
     * @return Builder
     * @throws Exception
     */
    public function setLoops(int $loops): self
    {
        if (count($this->gif->getFrames()) === 0) {
            throw new Exception('Add at least one frame before setting the loop count');
        }

        if ($loops >= 0) {
            // add frame count to existing or new netscape extension on first frame
            if (!$this->gif->getFirstFrame()->getNetscapeExtension()) {
                $this->gif->getFirstFrame()->addApplicationExtension(
                    new NetscapeApplicationExtension()
                );
            }
            $this->gif->getFirstFrame()->getNetscapeExtension()->setLoops($loops);
        }

        return $this;
    }

    /**
     * Create new animation frame from given source
     * which can be path to a file or GIF image data
     *
     * @param string $source
     * @param float $delay time delay in seconds
     * @param int $left position offset in pixels from left
     * @param int $top position offset in pixels from top
     * @return Builder
     */
    public function addFrame(string $source, float $delay = 0, int $left = 0, int $top = 0): self
    {
        $frame = new FrameBlock();
        $source = Decoder::decode($source);

        // store delay
        $frame->setGraphicControlExtension(
            $this->buildGraphicControlExtension(
                $source,
                intval($delay * 100)
            )
        );

        // store image
        $frame->setTableBasedImage(
            $this->buildTableBasedImage($source, $left, $top)
        );

        // add frame
        $this->gif->addFrame($frame);

        return $this;
    }

    /**
     * Build new graphic control extension with given delay & disposal method
     *
     * @param GifDataStream $source
     * @param int $delay
     * @param DisposalMethod $disposalMethod
     * @return GraphicControlExtension
     */
    protected function buildGraphicControlExtension(
        GifDataStream $source,
        int $delay,
        DisposalMethod $disposalMethod = DisposalMethod::BACKGROUND
    ): GraphicControlExtension {
        // create extension
        $extension = new GraphicControlExtension($delay, $disposalMethod);

        // set transparency index
        $control = $source->getFirstFrame()->getGraphicControlExtension();
        if ($control && $control->getTransparentColorExistance()) {
            $extension->setTransparentColorExistance();
            $extension->setTransparentColorIndex(
                $control->getTransparentColorIndex()
            );
        }

        return $extension;
    }

    /**
     * Build table based image object from given source
     *
     * @param GifDataStream $source
     * @param int $left
     * @param int $top
     * @return TableBasedImage
     */
    protected function buildTableBasedImage(GifDataStream $source, int $left, int $top): TableBasedImage
    {
        $block = new TableBasedImage();
        $block->setImageDescriptor(new ImageDescriptor());

        // set global color table from source as local color table
        $block->getImageDescriptor()->setLocalColorTableExistance();
        $block->setColorTable($source->getGlobalColorTable());

        $block->getImageDescriptor()->setLocalColorTableSorted(
            $source->getLogicalScreenDescriptor()->getGlobalColorTableSorted()
        );

        $block->getImageDescriptor()->setLocalColorTableSize(
            $source->getLogicalScreenDescriptor()->getGlobalColorTableSize()
        );

        $block->getImageDescriptor()->setSize(
            $source->getLogicalScreenDescriptor()->getWidth(),
            $source->getLogicalScreenDescriptor()->getHeight()
        );

        // set position
        $block->getImageDescriptor()->setPosition($left, $top);

        // add image data from source
        $block->setImageData($source->getFirstFrame()->getImageData());

        return $block;
    }

    /**
     * Encode the current build
     *
     * @return string
     */
    public function encode(): string
    {
        return $this->gif->encode();
    }
}
