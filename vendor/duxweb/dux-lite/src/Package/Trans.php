<?php

namespace Dux\Package;

class Trans
{
    public static function main(string $token, string $lang, array $data, string $content, callable $callback): void
    {
        $result = [];
        self::extractLeafNodes($data, $result);
        usort($result, function ($a, $b) {
            return strlen($b) - strlen($a);
        });
        $resultStr = implode("\n", array_values($result));


        $data = Package::request('post', '/v/services/trans', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $token
            ],
            'json' => [
                'content' => $resultStr,
                'lang' => $lang,
            ]
        ]);

        foreach ($data as $key => $vo) {
            $tmp = $content;
            foreach ($vo as $item) {
                $tmp = str_replace($item['src'], $item['dst'], $tmp);
            }
            $file = $callback($key);
            file_put_contents($file, $tmp);
        }

    }

    private static function extractLeafNodes($array, &$result = []): void
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::extractLeafNodes($value, $result);
            } else {
                $result[] = $value;
            }
        }
    }

}