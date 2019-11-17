<?php
declare(strict_types=1);

include_once 'tests/assets/classes/TestClass.php';

use PHPUnit\Framework\TestCase;
use Tests\Assets\Classes\TestClass;

class ContainerTest extends TestCase
{

    public function testConstructor()
    {
        $container = new Container();
        $this->assertInstanceOf(Container::class, $container);
    }


    public function testGet()
    {
        $container = new Container();
        $this->assertObjectHasMethod('get', $container);

        $instance = $container->get(TestClass::class);
        $this->assertNull($instance);
    }


    public function testHas()
    {
        $container = new Container();
        $this->assertObjectHasMethod('has', $container);
    }


    public function testSet()
    {
        $class = TestClass::class;

        $container = new Container();
        $this->assertObjectHasMethod('set', $container);

        $instance = $container->get($class);
        $this->assertNull($instance);

        $container->set([
            $class => Container::new($class, ['value1', 'value2'])
        ]);

        $constructor = $container->get($class);
        $this->assertInstanceOf(Closure::class, $constructor);

        $instance = $constructor();
        $this->assertInstanceOf($class, $instance);

    }


    public function testNew()
    {
        $class = TestClass::class;
        $constructor = Container::new($class, ['value1', 'value2']);
        //fwrite(STDERR, print_r($constructor, true));
        $this->assertInstanceOf(Closure::class, $constructor);

        $instance = $constructor('hallo', 'sonne');
        //fwrite(STDERR, print_r($instance, true));
        $this->assertInstanceOf($class, $instance);
    }


    public function assertObjectHasMethod(string $method, object $object, string $msg = '')
    {
        if (!method_exists($object, $method)) {
            $this->fail("Failed asserting that object of class \"" . get_class($object) . "\" has method \"$method\".");
        } else {
            $this->assertTrue(true);
        }
    }

}
