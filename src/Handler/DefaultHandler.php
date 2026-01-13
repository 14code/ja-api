<?php
declare(strict_types=1);

namespace I4code\JaApi\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class DefaultHandler extends RequestHandler
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = json_encode(['data' => 'test value']);
        $response = $this->createResponse(200, $body);
        return $response;
    }

}