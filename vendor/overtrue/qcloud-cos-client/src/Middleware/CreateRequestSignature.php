<?php

namespace Overtrue\CosClient\Middleware;

use Overtrue\CosClient\Signature;
use Psr\Http\Message\RequestInterface;

class CreateRequestSignature
{
    public function __construct(
        protected string $secretId,
        protected string $secretKey,
        protected ?string $signatureExpires = null
    ) {
    }

    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $request = $request->withHeader(
                'Authorization',
                (new Signature($this->secretId, $this->secretKey))
                    ->createAuthorizationHeader($request, $this->signatureExpires)
            );

            return $handler($request, $options);
        };
    }
}
