<?php
declare(strict_types=1);

namespace Tests\Assets\Classes;

class TestController
{
    private $dependant;

    public function __construct(TestClass $testClass)
    {
        $this->dependant = $testClass;
    }


    public function __invoke($request, $handler)
    {
        return 'hallo';
    }

}