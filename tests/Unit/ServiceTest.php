<?php
declare(strict_types=1);

namespace Tests\Unit;

use I4code\JaApi\ServerRequestFactory;
use I4code\JaApi\Service;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class ServiceTest extends TestCase
{

    public function testContructor()
    {
        $container = $this->createMock(ContainerInterface::class);
        $service = new Service($container);
        $this->assertInstanceOf(Service::class, $service);
    }


    public function testDispatch()
    {
        $serverRequest = (new ServerRequestFactory())->createTestRequest("GET", '/');
        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturn(true);
        $mwMock = $this->createMock(MiddlewareInterface::class);
        $container->method('get')->willReturn($mwMock);
        
        $service = new Service($container);
        $response = $service->dispatch($serverRequest);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

}
