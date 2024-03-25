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

use GuzzleHttp\Psr7\Uri as GuzzleUri;
use Nyholm\Psr7\Uri as NyholmUri;
use Slim\Http\Uri as SlimUri;
use Slim\Psr7\Factory\UriFactory as SlimPsr7UriFactory;
use Zend\Diactoros\Uri as ZendDiactorosUri;
use Laminas\Diactoros\Uri as LaminasDiactorosUri;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

final class UriFactory implements UriFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUri(string $uri = ""): UriInterface
    {
        if (class_exists(LaminasDiactorosUri::class)) {
            return new LaminasDiactorosUri($uri);
        }

        if (class_exists(NyholmUri::class)) {
            return new NyholmUri($uri);
        }

        if (class_exists(SlimPsr7UriFactory::class)) {
            return (new SlimPsr7UriFactory)->createUri($uri);
        }

        if (class_exists(ZendDiactorosUri::class)) {
            return new ZendDiactorosUri($uri);
        }

        if (class_exists(SlimUri::class)) {
            if (false === parse_url($uri)) {
                throw new \InvalidArgumentException("Invalid URI: $uri");
            }
            return SlimUri::createFromString($uri);
        }

        if (class_exists(GuzzleUri::class)) {
            return new GuzzleUri($uri);
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
