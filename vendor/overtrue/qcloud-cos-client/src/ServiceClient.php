<?php

namespace Overtrue\CosClient;

class ServiceClient extends Client
{
    public function getBuckets(string $region = null): Http\Response
    {
        $uri = $region ? \sprintf('https://cos.%s.myqcloud.com/', $region) : 'https://service.cos.myqcloud.com/';

        return $this->get($uri);
    }
}
