<?php

namespace Overtrue\CosClient;

use Psr\Http\Message\RequestInterface;

class Signature
{
    public const SIGN_HEADERS = [
        'host',
        'content-type',
        'content-md5',
        'content-disposition',
        'content-encoding',
        'content-length',
        'transfer-encoding',
        'range',
    ];

    public function __construct(public string $accessKey, public string $secretKey)
    {
    }

    public function createAuthorizationHeader(RequestInterface $request, int|string|\DateTimeInterface $expires = null): string
    {
        $signTime = self::getTimeSegments($expires ?? '+60 minutes');
        $queryToBeSigned = self::getQueryToBeSigned($request);
        $headersToBeSigned = self::getHeadersToBeSigned($request);

        $httpStringHashed = sha1(
            strtolower($request->getMethod())."\n".urldecode($request->getUri()->getPath())."\n".
            implode('&', array_values($queryToBeSigned)).
            "\n".\http_build_query($headersToBeSigned)."\n"
        );

        $stringToSign = \sprintf("sha1\n%s\n%s\n", $signTime, $httpStringHashed);
        $signature = hash_hmac('sha1', $stringToSign, hash_hmac('sha1', $signTime, $this->secretKey));

        return \sprintf(
            'q-sign-algorithm=sha1&q-ak=%s&q-sign-time=%s&q-key-time=%s&q-header-list=%s&q-url-param-list=%s&q-signature=%s',
            $this->accessKey,
            $signTime,
            $signTime,
            implode(';', array_keys($headersToBeSigned)),
            implode(';', array_keys($queryToBeSigned)),
            $signature
        );
    }

    protected static function getHeadersToBeSigned(RequestInterface $request): array
    {
        $headers = [];
        foreach ($request->getHeaders() as $header => $value) {
            $header = strtolower(urlencode($header));

            if (str_contains($header, 'x-cos-') || \in_array($header, self::SIGN_HEADERS)) {
                $headers[$header] = $value[0];
            }
        }

        ksort($headers);

        return $headers;
    }

    protected static function getQueryToBeSigned(RequestInterface $request): array
    {
        $query = [];
        foreach (explode('&', $request->getUri()->getQuery()) as $item) {
            if (! empty($item)) {
                $segments = explode('=', $item);
                $key = strtolower($segments[0]);
                if (count($segments) >= 2) {
                    $value = $segments[1];
                } else {
                    $value = '';
                }
                $query[$key] = $key.'='.$value;
            }
        }
        ksort($query);

        return $query;
    }

    protected static function getTimeSegments(int|string|\DateTimeInterface $expires = '+60 minutes'): string
    {
        $timezone = \date_default_timezone_get();

        date_default_timezone_set('PRC');

        // '900'/900
        if (is_numeric($expires)) {
            $expires = abs($expires);
        }

        $expires = match (true) {
            // 900/1700001234
            is_int($expires) => $expires >= time() ? $expires : time() + $expires,
            // '+60 minutes'/'2023-01-01 00:00:00'
            is_string($expires) => strtotime($expires),
            // new \DateTime('2023-01-01 00:00:00')
            $expires instanceof \DateTimeInterface => $expires->getTimestamp(),
            default => time() + 60,
        };

        $signTime = \sprintf('%s;%s', time() - 60, $expires);

        date_default_timezone_set($timezone);

        return $signTime;
    }
}
