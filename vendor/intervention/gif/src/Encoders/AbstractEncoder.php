<?php

declare(strict_types=1);

namespace Intervention\Gif\Encoders;

abstract class AbstractEncoder
{
    /**
     * Create new instance
     *
     * @param mixed $source
     */
    public function __construct(protected mixed $source)
    {
    }

    /**
     * Encode current source
     *
     * @return string
     */
    abstract public function encode(): string;
}
