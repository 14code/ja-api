<?php
declare(strict_types=1);

use I4code\JaApi\ServerRequestFactory;
use I4code\JaApi\Service;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{

    public function testContructor()
    {
        $service = new Service();
        $this->assertInstanceOf(Service::class, $service);
    }


    public function testDispatch()
    {
        $serverRequest = (new ServerRequestFactory())->createTestRequest("GET", '/');
        $service = new Service();
        $response = $service->dispatch($serverRequest);
        $this->assertInstanceOf(Response::class, $response);
    }

}
