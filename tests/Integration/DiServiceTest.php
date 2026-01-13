<?php
declare(strict_types=1);

namespace Tests\Integration;

use I4code\JaApi\Container as DI;
use I4code\JaApi\Factory\HttpFactory;
use I4code\JaApi\ServerRequestFactory;
use I4code\JaApi\Service;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TestClass;
use TestHandler;

class DiServiceTest extends TestCase
{

    public function testDi()
    {
        $psr17Factory = new Psr17Factory();
        $class = TestHandler::class;

        $container = new DI();
        $container->set([
            TestClass::class => DI::new(TestClass::class, ['value1', 'value2'])
        ]);
        $container->set([
            HttpFactory::class => DI::new(HttpFactory::class, [$psr17Factory])
        ])->set([
            TestHandler::class => DI::new(TestHandler::class, [
                $container->get(HttpFactory::class),
                $container->get(TestClass::class)
            ])
        ]);

        $service = new Service($container);
        $service->get('/ditest', $class);

        $serverRequestFactory = new ServerRequestFactory();
        $serverRequest = $serverRequestFactory->createTestRequest("GET", '/ditest');
        $response = $service->dispatch($serverRequest);

        $this->assertInstanceOf(Response::class, $response);
    }

}
