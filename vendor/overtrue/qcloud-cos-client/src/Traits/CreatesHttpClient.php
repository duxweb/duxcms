<?php

namespace Overtrue\CosClient\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

trait CreatesHttpClient
{
    protected array $options = [];

    protected array $middlewares = [];

    protected ?HandlerStack $handlerStack = null;

    public function createHttpClient(array $options = []): Client
    {
        return new Client(array_merge([
            'handler' => $this->getHandlerStack(),
        ], $this->options, $options));
    }

    public function setHttpClientOptions(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function mergeHttpClientOptions(array $options): static
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function getBaseUri()
    {
        return $this->options['base_uri'];
    }

    public function setBaseUri(string $baseUri): self
    {
        $this->options['base_uri'] = $baseUri;

        return $this;
    }

    public function setHeaders(array $headers): static
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    public function setHeader(string $name, string $value): static
    {
        if (empty($this->options['headers'])) {
            $this->options['headers'] = [];
        }

        $this->options['headers'][$name] = $value;

        return $this;
    }

    public function getHttpClientOptions(): array
    {
        return $this->options;
    }

    public function pushMiddleware(callable $middleware, string $name = null): static
    {
        if (! is_null($name)) {
            $this->middlewares[$name] = $middleware;
        } else {
            $this->middlewares[] = $middleware;
        }

        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function setMiddlewares(array $middlewares): static
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    public function setHandlerStack(HandlerStack $handlerStack): static
    {
        $this->handlerStack = $handlerStack;

        return $this;
    }

    public function getHandlerStack(): HandlerStack
    {
        if ($this->handlerStack) {
            return $this->handlerStack;
        }

        $this->handlerStack = HandlerStack::create();

        foreach ($this->middlewares as $name => $middleware) {
            $this->handlerStack->unshift($middleware, $name);
        }

        return $this->handlerStack;
    }
}
