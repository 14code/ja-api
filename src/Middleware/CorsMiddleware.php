<?php

// NOTE:
// This middleware intentionally does NOT support "*" with credentials.
// Browsers will reject such responses by specification.


namespace I4code\JaApi\Middleware;

use I4code\JaApi\CorsConfig;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    
    public function __construct(
        protected RequestHandlerInterface $optionsHandler,
        protected CorsConfig $config
    ) {}

    
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = strtoupper($request->getMethod());
        $origin = trim($request->getHeaderLine('Origin'));

        $isAllowedOrigin = $origin !== '' && in_array($origin, $this->config->allowedOrigins, true);

        $isPreflight =
            $method === 'OPTIONS'
            && $request->hasHeader('Origin')
            && $request->hasHeader('Access-Control-Request-Method');
        
        // Preflight
        if ($isPreflight) {
            $response = $this->optionsHandler->handle($request);
            return $this->withCorsHeaders($request, $response, $origin, $isAllowedOrigin, true);
        }

        $response = $handler->handle($request);
        return $this->withCorsHeaders($request, $response, $origin, $isAllowedOrigin, false);
    }


    /*
    CORS rules summary:
    - allowAnyOrigin=true  && allowCredentials=false → ACO="*"
    - allowAnyOrigin=false && whitelist match        → ACO="<origin>"
    - allowCredentials=true requires exact origin echo
    - allowAnyOrigin + allowCredentials is forbidden (spec violation)
    */
    private function withCorsHeaders(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $origin,
        bool $isAllowedOrigin,
        bool $isPreflight): ResponseInterface
    {
        // Always vary by Origin if CORS is in play (prevents proxy cache leakage)
        if ($origin !== '') {
            $response = $this->appendVary($response, 'Origin');
        }

        // If origin not allowed: do not emit allow-origin/credentials
        if (!$isAllowedOrigin && !$this->config->allowAnyOrigin) {
            // Still useful for preflight diagnostics: provide methods/headers/max-age even if origin not allowed?
            // Typically: omit everything except Vary. We'll keep it strict.
            return $response;
        }

        // Allow-Origin
        // If credentials are allowed, "*" is forbidden by browsers: must echo the specific origin.
        if ($this->config->allowAnyOrigin && $this->config->allowCredentials) {
            throw new LogicException('CORS config invalid: wildcard origin with credentials');
        }
        if ($this->config->allowAnyOrigin && !$this->config->allowCredentials) {
            $response = $response->withHeader('Access-Control-Allow-Origin',  '*');
        } else {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
        }

        // Allow-Credentials (only when configured)
        if ($this->config->allowCredentials) {
            $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        // Methods
        $methods = $this->config->allowedMethods ?: ['GET', 'OPTIONS'];
        $response = $response->withHeader('Access-Control-Allow-Methods', implode(', ', $methods));

        // Headers
        // If allowedHeaders not configured, fall back to requested headers (common convenience).
        $allowedHeaders = $this->config->allowedHeaders;
        if (!$allowedHeaders) {
            $requested = trim($request->getHeaderLine('Access-Control-Request-Headers'));
            if ($requested !== '') {
                // Normalize a bit: keep as-is but trim spaces.
                $allowedHeaders = array_map('trim', explode(',', $requested));
            } else {
                $allowedHeaders = ['Content-Type'];
            }
        }
        $response = $response->withHeader('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));

        // Max-Age for preflight caching
        if ($isPreflight && $this->config->maxAge > 0) {
            $response = $response->withHeader('Access-Control-Max-Age', (string)$this->config->maxAge);
        }

        return $response;
    }

    
    private function appendVary(ResponseInterface $response, string $value): ResponseInterface
    {
        $existing = $response->getHeaderLine('Vary');
        if ($existing === '') {
            return $response->withHeader('Vary', $value);
        }

        $parts = array_map('trim', explode(',', $existing));
        if (!in_array($value, $parts, true)) {
            $parts[] = $value;
        }

        return $response->withHeader('Vary', implode(', ', $parts));
    }

}