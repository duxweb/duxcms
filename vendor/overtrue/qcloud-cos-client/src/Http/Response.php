<?php

namespace Overtrue\CosClient\Http;

use JetBrains\PhpStorm\Pure;
use Overtrue\CosClient\Support\XML;
use Psr\Http\Message\ResponseInterface;

class Response extends \GuzzleHttp\Psr7\Response implements \ArrayAccess, \JsonSerializable
{
    protected ?array $arrayResult = null;

    public function __construct(ResponseInterface $response)
    {
        parent::__construct(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    public function toArray()
    {
        if (! \is_null($this->arrayResult)) {
            return $this->arrayResult;
        }

        $contents = $this->getContents();

        if (empty($contents)) {
            return $this->arrayResult = null;
        }

        return $this->arrayResult = $this->isXML() ? XML::toArray($contents) : \json_decode($contents, true);
    }

    public function toObject(): ?object
    {
        return \json_decode(\json_encode($this->toArray()));
    }

    public function isXML(): bool
    {
        return \strpos($this->getHeaderLine('content-type'), 'xml') > 0;
    }

    public function jsonSerialize(): mixed
    {
        try {
            return $this->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return \array_key_exists($offset, $this->toArray());
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }

    public static function create(
        $status = 200,
        array $headers = [],
        $body = null,
        $version = '1.1',
        $reason = null
    ): Response {
        return new self(new \GuzzleHttp\Psr7\Response($status, $headers, $body, $version, $reason));
    }

    public function toString(): string
    {
        return $this->getContents();
    }

    public function getContents(): string
    {
        $this->getBody()->rewind();

        return $this->getBody()->getContents();
    }

    #[Pure]
    final public function isInformational(): bool
    {
        return $this->getStatusCode() >= 100 && $this->getStatusCode() < 200;
    }

    #[Pure]
    final public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    #[Pure]
    final public function isRedirection(): bool
    {
        return $this->getStatusCode() >= 300 && $this->getStatusCode() < 400;
    }

    #[Pure]
    final public function isClientError(): bool
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }

    #[Pure]
    final public function isServerError(): bool
    {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }

    #[Pure]
    final public function isOk(): bool
    {
        return $this->getStatusCode() === 200;
    }

    #[Pure]
    final public function isForbidden(): bool
    {
        return $this->getStatusCode() === 403;
    }

    #[Pure]
    final public function isNotFound(): bool
    {
        return $this->getStatusCode() === 404;
    }

    #[Pure]
    final public function isEmpty(): bool
    {
        return \in_array($this->getStatusCode(), [204, 304]) || empty($this->getContents());
    }
}
