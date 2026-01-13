<?php
declare(strict_types=1);

namespace Tests\Unit;

use I4code\JaApi\ServerRequestFactory;
use I4code\JaApi\Service;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

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
        $service = new Service($container);
        $response = $service->dispatch($serverRequest);
        $this->assertInstanceOf(Response::class, $response);
    }

}
