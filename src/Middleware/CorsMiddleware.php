<?php

namespace I4code\JaApi\Middleware;

use I4code\JaApi\CorsConfig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    
    public function __construct(
        protected RequestHandlerInterface $optionsHandler,
        protected CorsConfig $corsConfig
    ) {}

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = strtoupper($request->getMethod());

        $isPreflight =
            $method === 'OPTIONS'
            && $request->hasHeader('Origin')
            && $request->hasHeader('Access-Control-Request-Method');
        
        // Preflight
        if ($isPreflight) {
            $response = $this->optionsHandler->handle($request);
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