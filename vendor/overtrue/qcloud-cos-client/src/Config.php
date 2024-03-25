<?php

namespace Overtrue\CosClient;

use ArrayAccess;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;

class Config implements ArrayAccess, JsonSerializable
{
    public function __construct(protected array $options)
    {
    }

    public function get(string $key, mixed $default = null)
    {
        $config = $this->options;

        if (isset($config[$key])) {
            return $config[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (! is_array($config) || ! array_key_exists($segment, $config)) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }

    public function set(string $key, mixed $value): array
    {
        $keys = explode('.', $key);
        $config = &$this->options;

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (! isset($config[$key]) || ! is_array($config[$key])) {
                $config[$key] = [];
            }
            $config = &$config[$key];
        }

        $config[array_shift($keys)] = $value;

        return $config;
    }

    public function has(string $key): bool
    {
        return (bool) $this->get($key);
    }

    public function missing(string $key): bool
    {
        return ! $this->has($key);
    }

    #[Pure]
    public function extend(array $options): Config
    {
        return new Config(\array_merge($this->options, $options));
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->options);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->set($offset, null);
    }

    public function jsonSerialize(): array
    {
        return $this->options;
    }

    public function __toString()
    {
        return \json_encode($this, \JSON_UNESCAPED_UNICODE);
    }
}
