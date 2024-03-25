<?php

declare(strict_types=1);

/*

Copyright (c) 2017-2021 Mika Tuupola

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

/**
 * @see       https://github.com/tuupola/http-factory
 * @license   http://www.opensource.org/licenses/mit-license.php
 */

namespace Tuupola\Http\Factory;

use GuzzleHttp\Psr7\UploadedFile as GuzzleUploadedFile;
use Nyholm\Psr7\UploadedFile as NyholmUploadedFile;
use Slim\Http\UploadedFile as SlimUploadedFile;
use Slim\Psr7\Factory\UploadedFileFactory as SlimPsr7UploadedFileFactory;
use Zend\Diactoros\UploadedFile as ZendDiactorosUploadedFile;
use Laminas\Diactoros\UploadedFile as LaminasDiactorosUploadedFile;

use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamInterface;

final class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUploadedFile(
        StreamInterface $stream,
        ?int $size = null,
        int $error = \UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ): UploadedFileInterface {
        if ($size === null) {
            $size = $stream->getSize();
        }

        if (class_exists(LaminasDiactorosUploadedFile::class)) {
            return new LaminasDiactorosUploadedFile(
                $stream,
                $size,
                $error,
                $clientFilename,
                $clientMediaType
            );
        }

        if (class_exists(NyholmUploadedFile::class)) {
            return new NyholmUploadedFile(
                $stream,
                $size,
                $error,
                $clientFilename,
                $clientMediaType
            );
        }

        if (class_exists(SlimPsr7UploadedFileFactory::class)) {
            return (new SlimPsr7UploadedFileFactory)->createUploadedFile(
                $stream,
                $size,
                $error,
                $clientFilename,
                $clientMediaType
            );
        }

        if (class_exists(ZendDiactorosUploadedFile::class)) {
            return new ZendDiactorosUploadedFile(
                $stream,
                $size,
                $error,
                $clientFilename,
                $clientMediaType
            );
        }

        if (class_exists(SlimUploadedFile::class)) {
            $meta = $stream->getMetadata();
            $file = $meta["uri"];

            if ($file === "php://temp") {
                $file = tempnam(sys_get_temp_dir(), "factory-test");
                file_put_contents($file, (string) $stream);
            }

            return new SlimUploadedFile(
                $file,
                $clientFilename,
                $clientMediaType,
                $size,
                $error
            );
        }

        if (class_exists(GuzzleUploadedFile::class)) {
            return new GuzzleUploadedFile(
                $stream,
                $size,
                $error,
                $clientFilename,
                $clientMediaType
            );
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
