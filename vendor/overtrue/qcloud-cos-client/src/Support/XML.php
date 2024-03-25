<?php

namespace Overtrue\CosClient\Support;

use TheNorthMemory\Xml\Transformer;

class XML
{
    public static function toArray(string $xml): array
    {
        return Transformer::toArray($xml);
    }

    public static function fromArray(array $data, string $root = 'xml', string $item = 'item'): bool|string
    {
        if (empty($data)) {
            return '';
        }

        return Transformer::toXml($data, root: $root, item: $item);
    }
}
