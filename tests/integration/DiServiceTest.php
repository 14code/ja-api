<?php
declare(strict_types=1);

include_once 'tests/assets/classes/TestController.php';
include_once 'tests/assets/classes/TestClass.php';

use I4code\JaApi\Container as DI;
use I4code\JaApi\ServerRequestFactory;
use I4code\JaApi\Service;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Classes\TestController;
use Tests\Assets\Classes\TestClass;

class DiServiceTest extends TestCase
{

    public function testDi()
    {
        $class = TestController::class;

        $service = new Service();
        $service->get('/ditest', $class);

        $container = new DI();
        $container->set([
            TestClass::class => DI::new(TestClass::class, ['value1', 'value2'])
        ]);
        $container->set([
            TestController::class => DI::new(TestController::class, [
                $container->get(TestClass::class)
            ])
        ]);
        $service->setContainer($container);

        $serverRequestFactory = new ServerRequestFactory();
        $serverRequest = $serverRequestFactory->createTestRequest("GET", '/ditest');
        $response = $service->dispatch($serverRequest);

        $this->assertInstanceOf(Response::class, $response);
    }

}
