<?php
declare(strict_types=1);

namespace Dux\Validator;

use ArrayAccess;

class Data implements ArrayAccess
{

    protected array $array = [];

    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    public function __set($key, $value)
    {
        $this->array[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->array[$key]);
    }

    public function __get($key)
    {
        return $this->array[$key] ?? null;
    }

    public function __unset($key)
    {
        if (isset($this->array[$key])) {
            unset($this->array[$key]);
        }
    }

    public function offsetSet($offset, $value): void
    {
        $this->array[$offset] = $value;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->array[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->array[$offset] ?? null;
    }

    public function offsetUnset($offset): void
    {
        if (isset($this->array[$offset])) {
            unset($this->array[$offset]);
        }
    }

    public function toArray(): array
    {
        return $this->array;
    }

}