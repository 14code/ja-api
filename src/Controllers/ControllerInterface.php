<?php
namespace I4code\JaApi\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface ControllerInterface
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler);
}