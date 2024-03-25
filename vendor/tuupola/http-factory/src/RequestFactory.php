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

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Nyholm\Psr7\Request as NyholmRequest;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Uri as SlimUri;
use Slim\Http\Headers as SlimHeaders;
use Slim\Psr7\Factory\RequestFactory as SlimPsr7RequestFactory;
use Zend\Diactoros\Request as ZendDiactorosRequest;
use Laminas\Diactoros\Request as LaminasDiactorosRequest;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class RequestFactory implements RequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (class_exists(LaminasDiactorosRequest::class)) {
            return new LaminasDiactorosRequest($uri, $method);
        }

        if (class_exists(NyholmRequest::class)) {
            return new NyholmRequest($method, $uri);
        }

        if (class_exists(SlimPsr7RequestFactory::class)) {
            return (new SlimPsr7RequestFactory)->createRequest($method, $uri);
        }

        if (class_exists(ZendDiactorosRequest::class)) {
            return new ZendDiactorosRequest($uri, $method);
        }

        if (class_exists(SlimRequest::class)) {
            $uri = SlimUri::createFromString($uri);
            $headers = new SlimHeaders;
            $body = (new StreamFactory)->createStream("");
            return new SlimRequest($method, $uri, $headers, [], [], $body);
        }

        if (class_exists(GuzzleRequest::class)) {
            return new GuzzleRequest($method, $uri);
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
