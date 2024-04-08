<?php

namespace Dux\Utils;

use Dux\App;
use GuzzleHttp\Client;
use Mimey\MimeTypes;
use Stevebauman\Hypertext\Transformer;
use voku\helper\HtmlDomParser;

class Content
{

    public static function extractDescriptions(?string $content = '', int $len = 255): string
    {
        $transformer = new Transformer();
        return mb_substr($transformer->toText($content), 0, $len);
    }

    public static function extractImages(?string $content = ''): array
    {
        if (!$content) {
            return [];
        }
        $images = [];
        $dom = HtmlDomParser::str_get_html($content);
        $elements = $dom->findMulti('img');
        foreach ($elements as $element) {
            $images[] = $element->getAttribute('src');
        }
        return $images;
    }

    /**
     * 本地化图片
     * @param array $images
     * @return array
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \League\Flysystem\FilesystemException
     */
    public static function localImages(array $images = []): array
    {
        if (!$images) {
            return [];
        }

        $list = App::config('storage')->get('drivers');
        $domain = [];
        foreach ($list as $vo) {
            $parseUrl = parse_url($vo['public_url']);
            $domain[] = $parseUrl['host'];
        }

        $client = new Client();

        $data = [];
        $mimes = new MimeTypes;
        foreach ($images as $url) {
            $parseUrl = parse_url($url);
            if (in_array($parseUrl['host'], $domain)) {
                $data[$url] = $url;
                continue;
            }

            try {
                $response = $client->get($url, [
                    'timeout' => 10,
                    'stream' => true,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.0.0',
                    ]
                ]);
            } catch (\Exception $e) {
                App::log('image')->error($e->getMessage());
                $data[$url] = $url;
                continue;
            }
            if ($response->getStatusCode() != 200) {
                App::log('image')->error($response->getBody()->getContents());
                $data[$url] = $url;
                continue;
            }
            $contentType = $response->getHeaderLine('Content-Type');
            $extension = $mimes->getExtension($contentType);
            $basename = bin2hex(random_bytes(10));
            $filename = sprintf('%s.%0.8s', $basename, $extension);
            $path = date('Y-m-d') . '/' . $filename;
            $stream = $response->getBody()->getContents();
            App::storage()->write($path, $stream);
            $resultUrl = App::storage()->publicUrl($path);
            if (!$resultUrl) {
                $data[$url] = $url;
                continue;
            }
            $data[$url] = $resultUrl;
        }
        return $data;
    }
}