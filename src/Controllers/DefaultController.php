<?php
declare(strict_types=1);

namespace I4code\JaApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


class DefaultController implements ControllerInterface
{

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        echo json_encode(['data' => 'test value']);
    }

}