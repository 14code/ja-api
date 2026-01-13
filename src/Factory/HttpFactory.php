<?php

namespace I4code\JaApi\Factory;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpFactory implements ResponseFactoryInterface, ServerRequestFactoryInterface
{
    
    public function __construct(
        private ResponseFactoryInterface&ServerRequestFactoryInterface $psr17
    ) {}

    
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return $this->psr17->createResponse($code, $reasonPhrase);
    }


    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return $this->psr17->createServerRequest($method, $uri, $serverParams);
    }

}