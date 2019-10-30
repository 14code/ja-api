<?php
declare(strict_types=1);

use I4code\JaApi\Middlewares\JsonMiddleware;
use PHPUnit\Framework\TestCase;
use I4code\JaApi\ServerRequestFactory;
use I4code\JaApi\Service;
use Nyholm\Psr7\Response;


class IntegrationTest extends TestCase
{

    public function testService()
    {
        $service = new Service();

        $service->addMiddleware(new JsonMiddleware());

        $service->get('/hallo', function () {
            echo '[xxx]';
        });

        $serverRequestFactory = new ServerRequestFactory();
        $serverRequest = $serverRequestFactory->createTestRequest("GET", '/hallo');
        $response = $service->dispatch($serverRequest);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', current($response->getHeader('Content-Type')));
        $this->assertJson((string) $response->getBody());
    }

}
