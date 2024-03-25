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

use GuzzleHttp\Psr7\ServerRequest as GuzzleServerRequest;
use Nyholm\Psr7\ServerRequest as NyholmServerRequest;
use Slim\Http\Request as SlimServerRequest;
use Slim\Http\Uri as SlimUri;
use Slim\Http\Headers as SlimHeaders;
use Slim\Psr7\Factory\ServerRequestFactory as SlimPsr7ServerRequestFactory;
use Zend\Diactoros\ServerRequest as ZendDiactorosServerRequest;
use Laminas\Diactoros\ServerRequest as LaminasDiactorosServerRequest;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        if (class_exists(LaminasDiactorosServerRequest::class)) {
            return new LaminasDiactorosServerRequest($serverParams, [], $uri, $method);
        }

        if (class_exists(NyholmServerRequest::class)) {
            return new NyholmServerRequest($method, $uri, [], null, "1.1", $serverParams);
        }

        if (class_exists(SlimPsr7ServerRequestFactory::class)) {
            return (new SlimPsr7ServerRequestFactory)->createServerRequest($method, $uri, $serverParams);
        }

        if (class_exists(ZendDiactorosServerRequest::class)) {
            return new ZendDiactorosServerRequest($serverParams, [], $uri, $method);
        }

        if (class_exists(SlimServerRequest::class)) {
            $uri = SlimUri::createFromString($uri);
            $headers = new SlimHeaders;
            $body = (new StreamFactory)->createStream("");
            return new SlimServerRequest($method, $uri, $headers, [], $serverParams, $body);
        }

        if (class_exists(GuzzleServerRequest::class)) {
            return new GuzzleServerRequest($method, $uri, [], null, "1.1", $serverParams);
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
