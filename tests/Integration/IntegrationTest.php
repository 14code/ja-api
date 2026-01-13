<?php
declare(strict_types=1);

namespace Tests\Integration;

use I4code\JaApi\Container;
use I4code\JaApi\Factory\HttpFactory;
use I4code\JaApi\Handler\DefaultHandler;
use I4code\JaApi\ServerRequestFactory;
use I4code\JaApi\Service;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;


class IntegrationTest extends TestCase
{

    public function testValidEndpoint()
    {
        $psr17Factory = new Psr17Factory();

        $container = new Container();
        $container->set([
            HttpFactory::class => Container::new(HttpFactory::class, [$psr17Factory]),
        ])->set([
            DefaultHandler::class => Container::new(DefaultHandler::class, [
                $container->get(HttpFactory::class)
            ]),
        ]);

        $service = new Service($container);
        
        $service->get('/test', function () {
            echo json_encode(['data' => 'test value']);
        });
        $service->get('/hallo', DefaultHandler::class);

        $serverRequestFactory = new ServerRequestFactory();
        $serverRequest = $serverRequestFactory->createTestRequest("GET", '/hallo');
        $response = $service->dispatch($serverRequest);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', current($response->getHeader('Content-Type')));

        $body = (string) $response->getBody();
        $this->assertJson($body);

        $fromJson = json_decode($body);
        $this->assertObjectHasProperty('data', $fromJson);
    }


    public function testInvalidEndpoint()
    {
        $container = new Container();
        $service = new Service($container);
        
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
        $this->assertObjectHasProperty('errors', $fromJson);
    }

}
