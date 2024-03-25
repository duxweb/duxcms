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

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Nyholm\Psr7\Response as NyholmResponse;
use Slim\Http\Response as SlimResponse;
use Slim\Psr7\Factory\ResponseFactory as SlimPsr7ResponseFactory;
use Zend\Diactoros\Response as ZendDiactorosResponse;
use Laminas\Diactoros\Response as LaminasDiactorosResponse;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createResponse(int $code = 200, string $reason = ""): ResponseInterface
    {
        if (class_exists(LaminasDiactorosResponse::class)) {
            return (new LaminasDiactorosResponse)->withStatus($code, $reason);
        }

        if (class_exists(NyholmResponse::class)) {
            return new NyholmResponse($code, [], null, "1.1", $reason);
        }

        if (class_exists(SlimPsr7ResponseFactory::class)) {
            return (new SlimPsr7ResponseFactory)->createResponse($code, $reason);
        }

        if (class_exists(ZendDiactorosResponse::class)) {
            return (new ZendDiactorosResponse)->withStatus($code, $reason);
        }

        if (class_exists(SlimResponse::class)) {
            return (new SlimResponse)->withStatus($code, $reason);
        }

        if (class_exists(GuzzleResponse::class)) {
            return new GuzzleResponse($code, [], null, "1.1", $reason);
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
