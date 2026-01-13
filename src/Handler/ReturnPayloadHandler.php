<?php
declare(strict_types=1);

namespace I4code\JaApi\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class ReturnPayloadHandler extends RequestHandler
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = (string) $request->getBody();
        return $this->createResponse(200, $body);
    }

}