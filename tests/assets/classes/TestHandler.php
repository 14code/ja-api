<?php
declare(strict_types=1);

use I4code\JaApi\Factory\HttpFactory;
use I4code\JaApi\Handler\RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestHandler extends RequestHandler
{
    private $dependant;

    public function __construct(HttpFactory $httpFactory, TestClass $testClass)
    {
        $this->dependant = $testClass;
        parent::__construct($httpFactory);
    }


    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = 'hallo';
        $response = $this->createResponse(200, $body);
        return $response;
    }

}