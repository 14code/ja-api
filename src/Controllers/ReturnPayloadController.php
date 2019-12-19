<?php
declare(strict_types=1);

namespace I4code\JaApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


class ReturnPayloadController implements ControllerInterface
{

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $body = (string) $request->getBody();
        echo $body;
    }

}