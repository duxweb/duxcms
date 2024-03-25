<?php

declare(strict_types=1);

namespace Intervention\Gif\Blocks;

use Intervention\Gif\AbstractEntity;

class FrameBlock extends AbstractEntity
{
    protected ?GraphicControlExtension $graphicControlExtension = null;
    protected ?ColorTable $colorTable = null;
    protected ?PlainTextExtension $plainTextExtension = null;
    protected array $applicationExtensions = [];
    protected array $commentExtensions = [];

    public function __construct(
        protected ImageDescriptor $imageDescriptor = new ImageDescriptor(),
        protected ImageData $imageData = new ImageData()
    ) {
    }

    public function addEntity(AbstractEntity $entity): self
    {
        switch (true) {
            case $entity instanceof TableBasedImage:
                $this->setTableBasedImage($entity);
                break;

            case $entity instanceof GraphicControlExtension:
                $this->setGraphicControlExtension($entity);
                break;

            case $entity instanceof ImageDescriptor:
                $this->setImageDescriptor($entity);
                break;

            case $entity instanceof ColorTable:
                $this->setColorTable($entity);
                break;

            case $entity instanceof ImageData:
                $this->setImageData($entity);
                break;

            case $entity instanceof PlainTextExtension:
                $this->setPlainTextExtension($entity);
                break;

            case $entity instanceof NetscapeApplicationExtension:
                $this->addApplicationExtension($entity);
                break;

            case $entity instanceof ApplicationExtension:
                $this->addApplicationExtension($entity);
                break;

            case $entity instanceof CommentExtension:
                $this->addCommentExtension($entity);
                break;
        }

        return $this;
    }

    public function getApplicationExtensions(): array
    {
        return $this->applicationExtensions;
    }

    public function getCommentExtensions(): array
    {
        return $this->commentExtensions;
    }

    public function setGraphicControlExtension(GraphicControlExtension $extension): self
    {
        $this->graphicControlExtension = $extension;

        return $this;
    }

    public function getGraphicControlExtension(): ?GraphicControlExtension
    {
        return $this->graphicControlExtension;
    }

    public function setImageDescriptor(ImageDescriptor $descriptor): self
    {
        $this->imageDescriptor = $descriptor;
        return $this;
    }

    public function getImageDescriptor(): ImageDescriptor
    {
        return $this->imageDescriptor;
    }

    public function setColorTable(ColorTable $table): self
    {
        $this->colorTable = $table;

        return $this;
    }

    public function getColorTable(): ?ColorTable
    {
        return $this->colorTable;
    }

    public function hasColorTable(): bool
    {
        return !is_null($this->colorTable);
    }

    public function setImageData(ImageData $data): self
    {
        $this->imageData = $data;

        return $this;
    }

    public function getImageData(): ImageData
    {
        return $this->imageData;
    }

    public function setPlainTextExtension(PlainTextExtension $extension): self
    {
        $this->plainTextExtension = $extension;

        return $this;
    }

    public function getPlainTextExtension(): ?PlainTextExtension
    {
        return $this->plainTextExtension;
    }

    public function addApplicationExtension(ApplicationExtension $extension): self
    {
        $this->applicationExtensions[] = $extension;

        return $this;
    }

    public function addCommentExtension(CommentExtension $extension): self
    {
        $this->commentExtensions[] = $extension;

        return $this;
    }

    public function getNetscapeExtension(): ?NetscapeApplicationExtension
    {
        $extensions = array_filter($this->applicationExtensions, function ($extension) {
            return $extension instanceof NetscapeApplicationExtension;
        });

        return count($extensions) ? reset($extensions) : null;
    }

    public function setTableBasedImage(TableBasedImage $tableBasedImage): self
    {
        $this->setImageDescriptor($tableBasedImage->getImageDescriptor());

        if ($colorTable = $tableBasedImage->getColorTable()) {
            $this->setColorTable($colorTable);
        }

        $this->setImageData($tableBasedImage->getImageData());

        return $this;
    }
}
