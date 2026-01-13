<?php

namespace I4code\JaApi\Handler;

use I4code\JaApi\Factory\HttpFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class RequestHandler implements RequestHandlerInterface
{
    public function __construct(protected HttpFactory $httpFactory)
    {}
    
    public function createResponse(int $code = 200, string $body = ''): ResponseInterface
    {
        $response = $this->httpFactory->createResponse($code);
        $bodyStream = $response->getBody();
        $bodyStream->rewind();
        $bodyStream->write($body);
        return $response;
    }

    /**
     * @inheritDoc
     */
    abstract public function handle(ServerRequestInterface $request): ResponseInterface;
}