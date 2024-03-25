<?php

declare(strict_types=1);

namespace Intervention\Gif\Decoders;

use Intervention\Gif\Blocks\ColorTable;
use Intervention\Gif\Blocks\ImageData;
use Intervention\Gif\Blocks\ImageDescriptor;
use Intervention\Gif\Blocks\TableBasedImage;

class TableBasedImageDecoder extends AbstractDecoder
{
    public function decode(): TableBasedImage
    {
        $block = new TableBasedImage();

        $block->setImageDescriptor(ImageDescriptor::decode($this->handle));

        if ($block->getImageDescriptor()->hasLocalColorTable()) {
            $block->setColorTable(
                ColorTable::decode(
                    $this->handle,
                    $block->getImageDescriptor()->getLocalColorTableByteSize()
                )
            );
        }

        $block->setImageData(
            ImageData::decode($this->handle)
        );

        return $block;
    }
}
