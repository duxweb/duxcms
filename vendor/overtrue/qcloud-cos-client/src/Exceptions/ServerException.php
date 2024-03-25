<?php

namespace Overtrue\CosClient\Exceptions;

use JetBrains\PhpStorm\Pure;
use Overtrue\CosClient\Http\Response;
use Psr\Http\Message\ResponseInterface;

class ServerException extends Exception
{
    protected \GuzzleHttp\Exception\ServerException $guzzleServerException;

    #[Pure]
    public function __construct(\GuzzleHttp\Exception\ServerException $guzzleServerException)
    {
        $this->guzzleServerException = $guzzleServerException;

        parent::__construct($guzzleServerException->getMessage(), $guzzleServerException->getCode(), $guzzleServerException->getPrevious());
    }

    public function getResponse(): ResponseInterface
    {
        return new Response($this->guzzleServerException->getResponse());
    }
}
