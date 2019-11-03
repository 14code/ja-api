<?php
declare(strict_types=1);

use I4code\JaApi\Middlewares\JsonMiddleware;
use PHPUnit\Framework\TestCase;
use I4code\JaApi\ServerRequestFactory;
use I4code\JaApi\Service;
use I4code\JaApi\Controllers\DefaultController;
use Nyholm\Psr7\Response;


class IntegrationTest extends TestCase
{

    public function testValidEndpoint()
    {
        $service = new Service();
        //$service->addMiddleware(new JsonMiddleware());
        $service->get('/test', function () {
            echo json_encode(['data' => 'test value']);
        });
        $service->get('/hallo', DefaultController::class);

        //fwrite(STDERR, __METHOD__ . "\n");
        $serverRequestFactory = new ServerRequestFactory();
        $serverRequest = $serverRequestFactory->createTestRequest("GET", '/hallo');
        $response = $service->dispatch($serverRequest);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', current($response->getHeader('Content-Type')));

        $body = (string) $response->getBody();
        $this->assertJson($body);

        $fromJson = json_decode($body);
        $this->assertObjectHasAttribute('data', $fromJson);
    }


    public function testInvalidEndpoint()
    {
        $service = new Service();
        //$service->addMiddleware(new JsonMiddleware());
        $service->get('/test', function () {
            echo json_encode(['test' => 'test value']);
        });

        //fwrite(STDERR, __METHOD__ . "\n");
        $serverRequestFactory = new ServerRequestFactory();
        $serverRequest = $serverRequestFactory->createTestRequest("GET", '/invalid');
        $response = $service->dispatch($serverRequest);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('application/json', current($response->getHeader('Content-Type')));

        $body = (string) $response->getBody();
        $this->assertJson($body);

        $fromJson = json_decode($body);
        $this->assertObjectHasAttribute('errors', $fromJson);
    }

}
