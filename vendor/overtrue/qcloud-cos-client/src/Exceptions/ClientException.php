<?php

namespace Overtrue\CosClient\Exceptions;

use JetBrains\PhpStorm\Pure;
use Overtrue\CosClient\Http\Response;
use Psr\Http\Message\ResponseInterface;

class ClientException extends Exception
{
    protected \GuzzleHttp\Exception\ClientException $guzzleClientException;

    #[Pure]
    public function __construct(\GuzzleHttp\Exception\ClientException $guzzleServerException)
    {
        $this->guzzleClientException = $guzzleServerException;

        parent::__construct($guzzleServerException->getMessage(), $guzzleServerException->getCode(), $guzzleServerException->getPrevious());
    }

    public function getResponse(): ResponseInterface
    {
        return new Response($this->guzzleClientException->getResponse());
    }
}
