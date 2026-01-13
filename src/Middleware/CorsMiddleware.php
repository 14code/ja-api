<?php

namespace I4code\JaApi\Middleware;

use I4code\JaApi\Factory\HttpFactory;
use I4code\JaApi\Handler\OptionsHandler;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Preflight
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            $httpFactory = new HttpFactory(new Psr17Factory());
            $response = (new OptionsHandler($httpFactory))->handle($request);
            return $this->withCorsHeaders($response);
        }

        $response = $handler->handle($request);
        return $this->withCorsHeaders($response);
    }

    private function withCorsHeaders(ResponseInterface $response): ResponseInterface
    {
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Vary', 'Origin');
    }
    
}