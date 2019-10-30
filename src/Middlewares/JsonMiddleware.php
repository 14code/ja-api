<?php
declare(strict_types=1);

namespace I4code\JaApi\Middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use I4code\JaApi\Exceptions\NoValidJsonResponseException;


class JsonMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $response = $response->withHeader("Content-Type", "application/json");

        $jsonTest = json_decode((string) $response->getBody());
        if (0 !== json_last_error()) {
            throw new NoValidJsonResponseException("Response does no contain JSON data.");
        }

        return $response;
    }

}