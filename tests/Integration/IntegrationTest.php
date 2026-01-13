<?php
declare(strict_types=1);

namespace Tests\Integration;

use I4code\JaApi\Container;
use I4code\JaApi\CorsConfig;
use I4code\JaApi\Factory\HttpFactory;
use I4code\JaApi\Handler\DefaultHandler;
use I4code\JaApi\Handler\OptionsHandler;
use I4code\JaApi\Middleware\CorsMiddleware;
use I4code\JaApi\ServerRequestFactory;
use I4code\JaApi\Service;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;


class IntegrationTest extends TestCase
{

    public function containerWithHttpFactory(Container $container): Container
    {
        $psr17Factory = new Psr17Factory();
        
        if (! $container->has(HttpFactory::class)) {
            $container->set([
                HttpFactory::class => Container::new(HttpFactory::class, [$psr17Factory]),
            ]);
        }
        
        return $container;
    }
    
    public function containerWithCorsMiddleware(Container $container): Container
    {
        $container = $this->containerWithHttpFactory($container);
        
        return $container->set([
            CorsMiddleware::class => Container::new(CorsMiddleware::class, [new OptionsHandler($container->get(HttpFactory::class)), new CorsConfig()])
        ]);
    }

    public function testValidEndpoint()
    {
        $psr17Factory = new Psr17Factory();

        $container = new Container();
        
        $container = $this->containerWithHttpFactory($container);
        
        $container->set([
            DefaultHandler::class => Container::new(DefaultHandler::class, [
                $container->get(HttpFactory::class)
            ]),
        ]);
        
        $container = $this->containerWithCorsMiddleware($container);

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
        $container = $this->containerWithCorsMiddleware($container);
        
        $service = new Service($container);
        
        $service->get('/test', function () {
            echo json_encode(['test' => 'test value']);
        });

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
    
    public function testCorsPreflight()
    {
        $psr17Factory = new Psr17Factory();

        $container = new Container();
        $container = $this->containerWithCorsMiddleware($container);
        $container->set([
            DefaultHandler::class => Container::new(DefaultHandler::class, [
                $container->get(HttpFactory::class)
            ]),
        ]);

        $service = new Service($container);

        $service->get('/test-cors', DefaultHandler::class);
        
        $serverRequestFactory = new ServerRequestFactory();
        $serverRequest = $serverRequestFactory->createTestRequest("OPTIONS", '/test-cors');
        $serverRequest = $serverRequest->withHeader('Origin', 'http://example.com');
        $serverRequest = $serverRequest->withHeader('Access-Control-Request-Method', 'GET, POST');
        $response = $service->dispatch($serverRequest);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(204, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertEmpty($body);
        $this->assertSame('*', current($response->getHeader('access-control-allow-origin')));
    }

}
